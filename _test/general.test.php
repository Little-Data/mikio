<?php

namespace dokuwiki\tpl\mikio\test;

use dokuwiki\template\mikio\mikio;
use DokuWikiTest;

/**
 * General tests for the advanced plugin
 *
 * @group tpl_bootstrap3
 * @group plugins
 */
class general_tpl_mikio_test extends DokuWikiTest
{

    /**
     * Simple test to make sure the template.info.txt is in correct format
     */
    public function test_templateinfo()
    {
        $file = __DIR__ . '/../template.info.txt';
        $this->assertFileExists($file);

        $info = confToHash($file);

        $this->assertArrayHasKey('base', $info);
        $this->assertArrayHasKey('author', $info);
        $this->assertArrayHasKey('email', $info);
        $this->assertArrayHasKey('date', $info);
        $this->assertArrayHasKey('name', $info);
        $this->assertArrayHasKey('desc', $info);
        $this->assertArrayHasKey('url', $info);

        $this->assertEquals('mikio', $info['base']);
        $this->assertRegExp('/^https?:\/\//', $info['url']);
        $this->assertTrue(mail_isvalid($info['email']));
        $this->assertRegExp('/^\d\d\d\d-\d\d-\d\d$/', $info['date']);
        $this->assertTrue(false !== strtotime($info['date']));
    }

/**
 * Test to ensure that every conf['...'] entry in conf/default.php has a corresponding meta['...'] entry in
 * conf/metadata.php.
 */
    public function test_tpl_conf()
    {
        $conf_file = __DIR__ . '/../conf/default.php';

        if (file_exists($conf_file)) {
            include $conf_file;
        }

        $meta_file = __DIR__ . '/../conf/metadata.php';

        if (file_exists($meta_file)) {
            include $meta_file;
        }

        $this->assertEquals(
            gettype($conf),
            gettype($meta),
            'Both conf/default.php and conf/metadata.php have to exist and contain the same keys.'
        );

        if (gettype($conf) != 'NULL' && gettype($meta) != 'NULL') {
            foreach ($conf as $key => $value) {
                $this->assertArrayHasKey(
                    $key,
                    $meta,
                    'Key $meta[\'' . $key . '\'] missing in conf/metadata.php'
                );
            }

            foreach ($meta as $key => $value) {
                $this->assertArrayHasKey(
                    $key,
                    $conf,
                    'Key $conf[\'' . $key . '\'] missing in conf/default.php'
                );
            }
        }
    }

    public function testSingletonPattern()
    {
        $instance1 = mikio::getInstance();
        $instance2 = mikio::getInstance();
        $this->assertSame($instance1, $instance2);
    }

    public function testGetConf()
    {
        $instance = mikio::getInstance();
        $this->assertFalse($instance->getConf('nonexistent_key'));
    }

    public function testIncludeFooter()
    {
        $instance = mikio::getInstance();
        $footer = $instance->includeFooter(false);
        $this->assertIsString($footer);
    }

    public function testUserCanEdit()
    {
        global $INFO;
        $INFO['isadmin'] = true;
        $instance = mikio::getInstance();
        $this->assertTrue($instance->userCanEdit());
    }

    public function testGetPageTitle()
    {
        global $conf;
        $conf['title'] = 'Test Wiki';
        $instance = mikio::getInstance();
        $title = $instance->getPageTitle();
        $this->assertStringContainsString('Test Wiki', $title);
    }
}
