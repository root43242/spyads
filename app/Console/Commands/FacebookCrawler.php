<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Panther\Client;
use GuzzleHttp\Client as GuzzleClient;
use App\Models\FacebookPage;
use Facebook\WebDriver\Exception\TimeoutException;



class FacebookAdsCrawler extends Command
{
    // Nome e descrição do comando
    protected $signature = 'crawler:facebook-ads';
    protected $description = 'Faz scraping da página do Facebook Ads Library';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Buscar todas as páginas do Facebook cadastradas no banco de dados
        $pages = FacebookPage::all();

        foreach ($pages as $page) {
            $attempt = 0; // Variável para contar tentativas de acessar a página

            // Iniciar loop para tentar acessar a página
            while (true) {
                try {
                    // Criar o cliente Panther (Chromium headless browser)
                    $client = Client::createChromeClient(null, [
                        '--headless',
                        '--disable-gpu',
                        '--no-sandbox',
                        '--disable-dev-shm-usage',
                        '--enable-logging', // Habilitar logging
                        '--v=1', // Verbosidade
                    ]);

                    // URL da página do Facebook Ads Library usando o page_id do banco de dados
                    $url = 'https://www.facebook.com/ads/library/?active_status=active&ad_type=all&country=ALL&media_type=all&search_type=page&view_all_page_id=' . $page->page_id;

                    // Fazer a requisição
                    $this->info("Acessando a página do Facebook Ads Library para a página ID: {$page->page_id}...");
                    $crawler = $client->request('GET', $url);

                    $client->waitFor('img.xl1xv1r', 120); // Aumente o timeout se necessário
                    $imageUrl = $crawler->filter('img.xl1xv1r')->attr('src');

                    // Aguarde até que a div com os resultados esteja carregada na página
                    $client->waitFor('.x8t9es0[role="heading"]', 120); // Aumente o timeout se necessário

                    // Pegue todos os elementos que correspondem ao seletor
                    $PageName = $crawler->filter('.x8t9es0[role="heading"]')->first()->text();

                    $client->waitFor('.x1uxerd5[role="heading"]', 120); // Aumente o timeout se necessário
                    $adsCountElements = $crawler->filter('.x1uxerd5[role="heading"]')->first()->text();

                    preg_match('/\d+/', $adsCountElements, $matches);
                    $adsCount = isset($matches[0]) ? $matches[0] : '0';

                    if ($page->last_activity_campaign === $adsCount) {
                        $this->info("Página já escaneada, nada mudou.");
                    } else {
                        $page->last_activity_campaign = $adsCount;
                        $page->save();
                    }

                    $webhookUrl = env('DISCORD_WEBHOOK');

                    // Criação do cliente Guzzle
                    $guzzleClient = new GuzzleClient();

                    // Corpo do Embed
                    $embed = [
                        [
                            'color' => hexdec('f5cb42'), // Cor do Embed (amarelo neste exemplo)
                            'author' => [
                                'name' => "{$PageName} - Essa página está com {$adsCount} ativos", // Nome do autor
                                'url' => $url, // URL do autor (clicável)
                                'icon_url' => $imageUrl, // URL da imagem do ícone do autor
                            ],
                            'fields' => [
                                [
                                    'name' => 'ACESSAR BIBLIOTECA', // Campo para o título no corpo
                                    'value' => $url, // Variável contendo o título do corpo
                                    'inline' => false
                                ],
                            ],
                            'footer' => [
                                'text' => 'Junin piroka de aço',
                            ],
                            'timestamp' => now()->toIso8601String(), // Timestamp do momento atual
                        ]
                    ];

                    // Corpo da requisição
                    $payload = [
                        'embeds' => $embed, // Embed gerado acima
                    ];

                    // Enviar a requisição POST para o Webhook do Discord
                    $response = $guzzleClient->post($webhookUrl, [
                        'json' => $payload
                    ]);

                    $this->info("Notificação enviada para {$PageName} com {$adsCount} anúncios ativos.");

                    // Saia do loop caso a requisição seja bem-sucedida
                    break;
                } catch (TimeoutException $e) {
                    $this->error("Timeout ao acessar a página {$page->page_id}. Tentando novamente em 3 minutos...");

                    // Esperar 3 minutos (180 segundos)
                    sleep(180);

                    // Incrementar o contador de tentativas
                    $attempt++;

                    // Definir um limite de tentativas (por exemplo, 3 tentativas)
                    if ($attempt >= 3) {
                        $this->error("Tentativas esgotadas para a página {$page->page_id}. Pulando para a próxima...");
                        break; // Sai do loop e continua com a próxima página
                    }
                } catch (\Exception $e) {
                    $this->error("Erro ao acessar a página {$page->page_id}: " . $e->getMessage());
                    break; // Sai do loop em caso de outro erro e vai para a próxima página
                }
            }

            // Esperar por 2 minutos antes de continuar para a próxima iteração
            sleep(120);
        }
    }

    
    

    

}
