<?php
namespace Test\App\Role;

use Tests\CommonTestCase;

class actionMapTest extends CommonTestCase
{

    // 添加权限测试
    public function testActionMap()
    {
        $params = [];

        $response = $this->json('GET', '/admin/actions/route', $params, $this->header);
        $result = json_decode($response->response->content(), true);
        $this->assertEquals(0, $result['errorCode']);
        $this->assertArrayHasKey('routes', $result['data']);

    }

    /**
     * 预加载数据
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet($this->dataSet('action/action-list.yaml'));
    }

}
