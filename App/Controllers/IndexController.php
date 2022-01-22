<?php

namespace App\Controllers;

use App\Repositories\Db;
use App\Repositories\DocumentQueryRepository;
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
           'docId' => 1
       ]);
    }

    /**
     * Возвращает вьюху с формой
     *
     * @param Environment $twig
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function actionGetDocFilters(Environment $twig)
    {
        $docId = $_GET['docId'] ?? null;
        if (null === $docId) {
            echo 'Не передан параметр docId';
            return;
        }

        echo $twig->render("filters_$docId.twig", [
            'docId' => $docId
        ]);
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
        $beginDate = $_POST['date']['begin'] ?? null;
        $endDate = $_POST['date']['end'] ?? null;
        $warehouseId = $_POST['warehouseId'] ?? null;
        $docId = $_POST['docId'] ?? null;

        if (!isset($beginDate, $endDate, $warehouseId, $docId)) {
            echo 'Некорректный запрос на страницу. Один из параметров (date["begin"], date["end"], warehouseId, docId)';
            return;
        }

        $beginDate = \DateTimeImmutable::createFromFormat('d.m.Y H:i', $beginDate);
        $endDate = \DateTimeImmutable::createFromFormat('d.m.Y H:i', $endDate);
        if (false === $beginDate && false === $endDate) {
            echo 'Неверный формат времени. Требуется формат d.m.Y H:i';
            return;
        }

        $documentRepository = new DocumentQueryRepository($db);
        $docModel = $documentRepository->findByPk((int)$docId);
        $sql = $docModel->getQueryString();

        $rows = $db->query($sql, null, [
            ':beginDate' => $beginDate->format('Y-m-d H:i:s'),
            ':endDate' => $endDate->format('Y-m-d H:i:s'),
            ':warehouseId' => $warehouseId,
        ]);

        $spreadsheet = (new WarehouseReportXlsService())->getXls($rows);
        $writer = new Xlsx($spreadsheet);
        $fileName = (new \DateTimeImmutable())->format('Ymd_Hisv_') . "warehouseReport_$warehouseId";
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

        unlink($fileName);
    }
}
