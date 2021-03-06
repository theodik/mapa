<?php

/**
 * Hlavní třída aplikace
 *
 * Globální singleton, načítá configy, vytváří controller podle požadavku.
 */
class Application {

  protected static $instance = null;
  private $config, $router;

  private function __construct() {
  }

  /**
   * Vrací instanci aplikace
   *
   * @return Application Instance applikace
   */
  public static function instance() {
    if (!self::$instance) {
      self::$instance = new Application();
    }

    return self::$instance;
  }

  /**
   * Nastavení aplikace
   *
   * Načítá configy a připojuje k databázi.
   */
  public function init() {
    $this->config = $this->loadConfig(ROOT_DIR . '/config/application.json');
    $databaseConfig = $this->loadConfig(ROOT_DIR . '/config/database.json');
    R::setup($databaseConfig->{APP_ENV}->uri, $databaseConfig->{APP_ENV}->username, $databaseConfig->{APP_ENV}->password);
    R::exec('set names utf8');
    if (APP_ENV == 'production') {
      R::freeze(true);
    }
    $this->router = new AltoRouter();
  }

  /**
   * Hlavní metoda aplikace
   *
   * Vytvoří controller a zavolá akci.
   */
  public function run() {
    $router = $this->router;
    $router->setBasePath($this->config->basePath);
    include(ROOT_DIR . '/config/routes.php');
    $match = $router->match();

    if ($match === false) {
      http_response_code(404);
      $this->render_404();
      return;
    }

    $params = new Params(array_merge($match['params'], array('action' => $this->getAction($match['target']))));

    $controllerClass = $this->getController($match['target']);
    $controller = new $controllerClass($this, $params);
    $controller->render_view($this->getAction($match['target']));
  }

  /**
   * Ukončuje aplikaci
   */
  public function finalize() {
    R::close();
    self::$instance = null;
  }

  /**
   * Načte soubor s nastavením
   *
   * @param string $fileName Cesta k souboru pro načtení
   * @return stdClass config
   */
  public function loadConfig($fileName = null) {
    $contents = utf8_encode(file_get_contents($fileName));
    return json_decode($contents);
  }

  /**
   * @return stdClass Nastavení aplikace
   */
  public function config() {
    return $this->config;
  }

  /**
   * @return AltoRouter Router cest
   */
  public function getRouter() {
    return $this->router;
  }

  /**
   * Vrací controller podle matchnuté akce z routeru
   *
   * @param string $string action match
   * @return Controller vytvořený controller
   */
  protected function getController($string) {
    $split = explode('#', $string);
    $klass = $split[0];
    return ucfirst($klass) . 'Controller';
  }

  /**
   * Vrátí konkrétní akci z route matche
   *
   * @param string $string action match
   * @return string action
   */
  protected function getAction($string) {
    $split = explode('#', $string);
    $action = $split[1];
    return $action;
  }

  /**
   * Zobrazí 404 stránku
   */
  protected function render_404() {
    $filename = ROOT_DIR . '/app/views/404.html';
    if (file_exists($filename)){
      echo file_get_contents($filename);
    } else {
      echo "<html><head><meta charset=\"utf-8\"></head><body><h1>404 Not Found</h1><hr>"
        ."Additional error while rendering 404 file: File `$filename' not found.</body></html>";
    }
  }
}
