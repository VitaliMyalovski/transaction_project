<?php


namespace App\Http\Controllers\Reports;


use App\Http\Controllers\Controller;
use App\Service\CurrencyService;
use App\Service\TransactionService;
use App\Service\UserService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

    private TransactionService $transactionService;
    private CurrencyService $currencyService;
    private UserService $userService;

    public function __construct(
        TransactionService $transactionService,
        CurrencyService $currencyService,
        UserService $userService
    ) {
        $this->transactionService = $transactionService;
        $this->currencyService = $currencyService;
        $this->userService = $userService;
    }

    public function index()
    {
        return view('reports.transaction', []);
    }

    public function getData(Request $request)
    {
        $userId = $this->userService->getUserId($request->name, $request->country, $request->city_of_registration);

        $data = $this->transactionService->getAlltransactionForUser($userId, $request->date_from, $request->date_to, 100000);

        $currentSum = 0;
        $usdSum = 0;
        $data->each(function ($item) use (&$usdSum, &$currentSum) {
            $usdSum += $this->currencyService->convertToUsd($item->value, $item->quotation);
            $currentSum += abs($item->value);
            unset($item->quotation);
        });

        return response()->json([
            'data' => $data,
            'currentSum' => round($currentSum, 2),
            'usdSum' => round($usdSum, 2),
            'error' => null
        ],
            200);
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'export.csv';

        $data = $this->getData($request);
        $data = \Response::json($data)->getData('data')['original']['data'];

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Дата операции', 'Изменение баланса', 'Тип операции');

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $item) {
                $row['created_at'] = $item['created_at'];
                $row['value'] = $item['value'];
                $row['type_oper'] = $item['type_oper'];

                fputcsv($file,
                    array($row['created_at'], $row['value'], $row['type_oper']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
