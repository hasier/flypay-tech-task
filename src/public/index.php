<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../classes/exceptions.php';
require_once __DIR__ . '/../classes/factories.php';
require_once __DIR__ . '/../classes/finders.php';
require_once __DIR__ . '/../classes/payment.php';
require_once __DIR__ . '/../classes/validators.php';
require_once __DIR__ . '/../classes/serializers.php';

$config['displayErrorDetails'] = getenv('IN_DEV') == 'true';

$config['db']['host'] = getenv('DB_HOST');
$config['db']['user'] = getenv('DB_USER');
$config['db']['pass'] = getenv('DB_PASS');
$config['db']['dbname'] = getenv('DB_NAME');

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

$container['errorHandler'] = function ($container) {
    return new ExceptionHandler();
};

$container['db'] = function ($container) {
    $db = $container['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$auth = function ($request, $response, $next) {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
      throw new AuthException('Credentials not supplied');
    }
    $user = (new UserFinder($this->db))->getUserByToken($_SERVER['PHP_AUTH_USER']);
    if (is_null($user)) {
      throw new AuthException('User not found');
    }

    return $next($request->withAttribute('user', $user), $response);  // Save the user just in case we need to use it later
};

function validate(callable $validator) {
   return function ($request, $response, $next) use ($validator) {
      $data = null;
      if ($request->isGet()) {
        $data = $request->getQueryParams();
      } elseif ($request->isPost()) {
        $data = $request->getParsedBody();
      }
      $filteredData = call_user_func($validator, $data);
      if ($filteredData === false) {
        throw new ValidationException('Invalid parameters: ' . json_encode($data));
      }
      return $next($request, $response);
   };
};

$app->post('/payments', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $newPayment = (new PaymentFactory($this->db))->save($filteredData);
    return $response->withJson(PaymentSerializer::serializePost($newPayment), 201);
})->add(validate('PaymentValidator::validatePost'))->add($auth);

$app->get('/payments', function (Request $request, Response $response) {
    $params = $request->getQueryParams();
    $location = null;
    if (isset($params['location'])) {
      $location = $params['location'];
    }
    $payments = (new PaymentFinder($this->db))->getPaymentsFromDate((new DateTime())->modify('-24 hours'), $location);
    return $response->withJson(PaymentSerializer::serializeList($payments, 'PaymentSerializer::serializeGet'));
})->add(validate('PaymentValidator::validateGet'))->add($auth);

$app->run();
