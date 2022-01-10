<?php

namespace App\Core\Console\Commands;

use App\Helpers\DateHelper;
use App\Helpers\FileHelper;
use App\Helpers\StringHelper;
use App\Services\HttpClient;
use App\Services\Package;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CrawlerStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Minhas Importacoes Crawler';

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var Package
     */
    private $package;

    /**
     * @var string
     */
    private $viewState;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->httpClient = new HttpClient();
        $this->package = new Package();
    }

    /**
     * @return void
     */
    public function handle()
    {
        try {
            $this->login();
            $this->getPackagesInfo();

            $this->info('Crawler Executed!');
            Log::info('Crawler Executed!');
        } catch (GuzzleException $e) {
            dump($e->getMessage());
            $this->error('Error in HTTP Client');
        } finally {
            unset($this->httpClient);
            FileHelper::clearCookiesFolder();
        }
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    private function getExecutionToken(): string
    {
        $content = $this->httpClient
            ->request('GET', 'https://cas.correios.com.br/login?service=https%3A%2F%2Fapps.correios.com.br%2Fportalimportador%2Fpages%2FpesquisarRemessaImportador%2FpesquisarRemessaImportador.jsf')
            ->getBody()
            ->getContents();

        $token = StringHelper::doRegex(StringHelper::clearPageContent($content), '/execution[\w\W]+?value=\"([\w\W]+?)\"/i');
        return $token[1][0];
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    private function login()
    {
        $token = $this->getExecutionToken();

        $this->httpClient->request('POST', 'https://cas.correios.com.br/login?service=https://apps.correios.com.br/portalimportador/pages/pesquisarRemessaImportador/pesquisarRemessaImportador.jsf', [
            'headers' => $this->getHeaders(),
            'form_params' => [
                'username' => env('MINHAS_IMPORTACOES_USERNAME'),
                'password' => env('MINHAS_IMPORTACOES_PASSWORD'),
                'execution' => $token,
                '_eventId' => 'submit',
                'geolocation' => ''
            ]
        ]);
    }

    /**
     * @param int $page
     * @return void
     * @throws GuzzleException
     */
    private function getPackagesInfo(int $page = 1)
    {
        $method = $this->getMethod($page);
        $content = $this->httpClient->request($method, 'https://apps.correios.com.br/portalimportador/pages/pesquisarRemessaImportador/pesquisarRemessaImportador.jsf', [
            'headers' => $this->getHeaders(),
            'form_params' => $this->getBody($page)
        ])
            ->getBody()
            ->getContents();

        $maxPage = $this->getMaxPage($content);
        $this->getViewState($content);
        $this->getPackagesTable(StringHelper::clearPageContent($content));

        if ($page < $maxPage) {
            $page += 1;
            $this->getPackagesInfo($page);
        }
    }

    /**
     * @param string $content
     * @return void
     */
    private function getPackagesTable(string $content)
    {
        $table = StringHelper::doRegex($content, '/<table[\w\W]+?<\/table>/i');
        $table = $table[0][0];

        $this->getPackagesTableRows($table);
    }

    private function getPackagesTableRows(string $table)
    {
        $rows = StringHelper::doRegex($table, '/<tr><td[\w\W]+?<\/tr>/i');

        foreach ($rows[0] as $row) {
            $rowData = $this->getData($row);

            $trackingNumber = $rowData[1];
            $status = $rowData[4];
            $date = DateHelper::parse($rowData[5]);

            if ($this->existsTrackingNumberInDatabase($trackingNumber) === false) {
                $this->package->storePackage($trackingNumber, $status, $date);
            }
        }
    }

    /**
     * @param string $row
     * @return array
     */
    private function getData(string $row): array
    {
        $data = StringHelper::doRegex($row, '/<td>(|[\w\W]+?)<\/td>/i');
        return $data[1];
    }

    /**
     * @param string $trackingNumber
     * @return bool
     */
    private function existsTrackingNumberInDatabase(string $trackingNumber): bool
    {
        $package = $this->package->getPackageByTrackingNumber($trackingNumber);

        if (!empty($package)) {
            return true;
        }

        return false;
    }

    /**
     * @return string[]
     */
    private function getHeaders(): array
    {
        return [
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0'
        ];
    }

    /**
     * @param string $content
     * @return int
     */
    private function getMaxPage(string $content): int
    {
        $maxPage = StringHelper::doRegex($content, '/de\s([\d]+)/i');

        return $maxPage[1][0];
    }

    /**
     * @param int $page
     * @return string
     */
    private function getMethod(int $page): string
    {
        if ($page > 1) {
            return 'POST';
        }

        return 'GET';
    }

    /**
     * @param int $page
     * @return array
     */
    private function getBody(int $page): array
    {
        if ($page > 1) {
            return [
                'form-pesquisarRemessas' => 'form-pesquisarRemessas',
                'form-pesquisarRemessas:codigoEncomenda' => '',
                'form-pesquisarRemessas:j_idt65:j_idt77' => '1',
                'form-pesquisarRemessas:j_idt116:j_idt128' => $page,
                'javax.faces.ViewState' => $this->viewState,
                'javax.faces.source' => 'form-pesquisarRemessas:j_idt116:j_idt128',
                'javax.faces.partial.event' => 'keyup',
                'javax.faces.partial.execute' => 'form-pesquisarRemessas:j_idt116:j_idt128',
                'javax.faces.partial.render' => 'form-pesquisarRemessas',
                'javax.faces.behavior.event' => 'keyup',
                'javax.faces.partial.ajax' => 'true'
            ];
        }

        return [];
    }

    /**
     * @param string $content
     * @return void
     */
    private function getViewState(string $content)
    {
        $viewState = StringHelper::doRegex($content, '/viewstate\"[\w\W]value=\"([\w\W]+?)\"/i');
        $this->viewState = $viewState[1][0];
    }
}
