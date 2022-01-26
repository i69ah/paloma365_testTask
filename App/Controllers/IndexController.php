<?php

namespace App\Controllers;

use App\Repositories\Db;
use App\Repositories\DocumentQueryRepository;
use App\Services\AttributeValidatorService;
use App\Services\FiltersService;
use App\Services\FormBuilderService;
use App\Services\WarehouseReportXlsService;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class IndexController
 * Основной контроллер
 *
 * @package App\Controllers
 */
class IndexController extends Controller
{
    protected string $cacheFile = 'temp/cache.json';

    /**
     * Отображает основнуб страницу
     *
     * @param Environment $twig
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function actionIndex(Environment $twig)
    {
       echo $twig->render('index.twig', [
           'data' => [
               'Фильтровать документы',
               'Фильтровать сотрудников',
           ]
       ]);
    }

    /**
     * Возвращает вьюху с формой
     *
     * @param Environment $twig
     * @param Db $db
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function actionGetDocFilters(Environment $twig, Db $db)
    {
        $docId = $_GET['docId'] ?? null;
        if (null === $docId) {
            echo 'Не передан параметр docId';
            return;
        }

        $documentQueryRepository = new DocumentQueryRepository($db);
        $documentQuery = $documentQueryRepository->findByPk((int)$docId);
        $filtersArray = (new FiltersService())->checkTypes($documentQuery->getFilters(), $db);
        $formArray = (new FormBuilderService())->buildFormFields($filtersArray, $twig);
        $formArray[] = (new FormBuilderService())->buildHiddenInput('docId', $docId, $twig);

        echo $twig->render("formTemplate.twig", [ 'formArray' => $formArray ]);
    }

    /**
     * Собирает документ и возвращает его пользователю
     *
     * @param Environment $twig
     * @param Db $db
     * @throws Exception
     */
    public function actionCreateDoc(Environment $twig, Db $db)
    {
        $docId = $_POST['docId'] ?? null;
        if (null === $docId) {
            echo 'Не передан параметр docId';
            return;
        }

        $documentQueryRepository = new DocumentQueryRepository($db);
        $documentQuery = $documentQueryRepository->findByPk((int)$docId);
        $filtersArray = (new FiltersService())->checkTypes($documentQuery->getFilters(), $db);
        $filterNamesArray = array_keys($filtersArray);
        if (!(new AttributeValidatorService())->validatePostAttributes($filtersArray, $_POST)) {
            echo 'Некорректный запрос на страницу. Один из параметров ' . implode(', ', $filterNamesArray);
            return;
        }

        if (!(new AttributeValidatorService())->validateDateFormat($filtersArray, $_POST)) {
            echo 'Неверный формат времени. Требуется формат d.m.Y H:i';
            return;
        }

        $sql = $documentQuery->getQueryString();
        $data = (new FiltersService())->buildParamsArray($filtersArray, $_POST);

        $rows = $db->query($sql, null, $data);

        $fh = fopen($this->cacheFile, 'w');
        $data = [
            $docId => [
                'rows' => $rows,
                'headers' => (new FiltersService())->buildHeadersArray($filtersArray)
            ]
        ];
        fwrite($fh, json_encode($data));
        fclose($fh);

        echo $twig->render('table.twig', ['data' => $data, 'docId' => $docId]);
    }

    /**
     * Сборка xls файла из кэша
     *
     * @param Environment $twig
     * @param Db $db
     * @throws Exception
     */
    public function actionCreateXls(Environment $twig, Db $db)
    {
        if (!file_exists($this->cacheFile)) {
            echo 'Невозможно собрать xls';
        }

        $fileArray = json_decode(file_get_contents($this->cacheFile), true);
        $docId = array_keys($fileArray)[0];
        $spreadsheet = (new WarehouseReportXlsService())->getXls($fileArray[$docId]);
        $writer = new Xlsx($spreadsheet);
        $fileName = (new \DateTimeImmutable())->format('Ymd_Hisv') . "_report_$docId";
        $fileName = "temp/$fileName.xlsx";

        $writer->save($fileName);

        if (file_exists($fileName)) {

            // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }
            // заставляем браузер показать окно сохранения файла
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($fileName));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileName));
            // читаем файл и отправляем его пользователю
            if ($fd = fopen($fileName, 'rb')) {
                while (!feof($fd)) {
                    print fread($fd, 1024);
                }
                fclose($fd);
            }
        }

        unlink($this->cacheFile);
        unlink($fileName);
    }
}
