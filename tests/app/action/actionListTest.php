<?php
namespace Test\App\Role;

use Tests\CommonTestCase;

class actionListTest extends CommonTestCase
{

    // 权限列表测试
    public function testActionList()
    {
        $response = $this->json('GET', '/admin/actions', [], $this->header);
        $result = json_decode($response->response->content(), true);
        $this->assertEquals(2, count($result['data']));

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
