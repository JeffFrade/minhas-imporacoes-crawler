<?php

namespace App\Core\Console\Commands;

use App\Helpers\FileHelper;
use App\Helpers\StringHelper;
use App\Services\HttpClient;
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->httpClient = new HttpClient();
    }

    /**
     * @return void
     */
    public function handle()
    {
        try {
            $this->login();
            $this->getPackagesInfo();

            $this->info('Crawler Executed');
            Log::info('Crawler Executed');
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

        $token = StringHelper::doRegex($content, '/execution[\w\W]+?value=\"([\w\W]+?)\"/i');
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
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0'
            ],

            'form_params' => [
                'username' => env('MINHAS_IMPORTACOES_USERNAME'),
                'password' => env('MINHAS_IMPORTACOES_PASSWORD'),
                'execution' => $token,
                '_eventId' => 'submit',
                'geolocation' => ''
            ]
        ]);
    }

    private function getPackagesInfo()
    {
        $content = $this->httpClient->request('GET', 'https://apps.correios.com.br/portalimportador/pages/pesquisarRemessaImportador/pesquisarRemessaImportador.jsf;jsessionid=LlWRvDWT4DTRQJZ9cM5OIeP1')
            ->getBody()
            ->getContents();

        dump($content);
    }
}
