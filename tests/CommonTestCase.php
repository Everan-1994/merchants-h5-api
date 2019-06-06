<?php

namespace Tests;

use Dotenv\Dotenv;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\Yaml\Yaml;

abstract class CommonTestCase extends TestCase
{
//    use DatabaseMigrations;
    use DatabaseTestCaseTrait{
        setUp as databaseSetUp;
        tearDown as databaseTearDown;
    }

    public static $hasInited = false;

    public function setUp()
    {
        // env文件切换
        if (!file_exists(__DIR__.'/.env')) {
            exit('tests目录下缺少.env文件');
        }
        (new Dotenv(__DIR__))->overload();

        $hostSet = ['localhost', '127.0.0.1', 'mysql'];
        if (!in_array(getenv('DB_HOST'), $hostSet)) {
            exit('DB_HOST必须为localhost或127.0.0.1');
        }

        parent::setUp();
        if (false == CommonTestCase::$hasInited) {
            self::initSystemDataSet();
            CommonTestCase::$hasInited = true;
        }
        $this->databaseSetUp();
        $this->auth();
    }

    /**
     * 初始化系统数据.
     */
    public function initSystemDataSet()
    {
        $this->getDatabaseTester()->setDataSet($this->createArrayDataSet($this->dataSet('system/init.yaml')));
        $this->getDatabaseTester()->onSetUp();
    }

    /**
     * 预加载数据.
     *
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet($this->dataSet('block/list.yaml'));
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->databaseTearDown();
    }

    /**
     * 公共请求头.
     *
     * @var array
     */
    protected $header = [];

    /**
     * 登录认证
     */
    public function auth()
    {
        $params = [
            'email' => 'admin@douanquan.com',
            'password' => 'admin',
        ];
        $response = $this->json('POST', '/admin/login', $params);
        $result = json_decode($response->response->content(), true);
        $this->header['Authorization'] = 'Bearer '.$result['data']['meta']['accessToken'];
    }

    /**
     * @param $file
     *
     * @return mixed
     */
    protected function dataSet($file)
    {
        $path = __DIR__.'/fixtures/'.$file;

        return Yaml::parse(file_get_contents($path));
    }

    /**
     * @return \PHPUnit\DbUnit\Database\DefaultConnection
     */
    protected function getConnection()
    {
        static $pdo;
        if (!$pdo) {
            /** @var MySqlConnection $connection */
            $connection = DB::connection();
            $pdo = $connection->getPDO();
        }

        return $this->createDefaultDBConnection($pdo, getenv('DB_DATABASE'));
    }
}
