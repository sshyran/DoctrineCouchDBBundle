<?php


namespace Doctrine\Bundle\CouchDBBundle\Tests\DependencyInjection;

use Doctrine\Bundle\CouchDBBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $config;
    /**
     * @var Processor
     */
    private $processor;

    public function setUp()
    {
        $this->config = new Configuration(false);
        $this->processor = new Processor();
    }

    public function testEmptyConfig()
    {
        // , 'The child node "client" at path "doctrine_couch_db" must be configured.'
        $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        $this->processor->processConfiguration($this->config, array());
    }

    public function testEmptyClientLeadsToDefaultConnection()
    {
        $config = $this->processor->processConfiguration($this->config, array(array('client' => null)));

        $this->assertEquals(array(
            'client' => array('default_connection' => 'default', 'connections' => array(
                'default' => array('host' => 'localhost', 'port' => 5984, 'user' => null, 'password' => null, 'ip' => null, 'logging' => false, 'url' => null, 'ssl' => false, 'timeout' => 0.01)
            ))
        ), $config);
    }

    public function testSingleTopLevelClientConnectionDefinition()
    {
        $config = $this->processor->processConfiguration($this->config, array(
            array(
                'client' => array(),
            )
        ));

        $this->assertEquals(array(
            'client' => array('default_connection' => 'default', 'connections' => array(
                'default' => array('host' => 'localhost', 'port' => 5984, 'user' => null, 'password' => null, 'ip' => null, 'logging' => false, 'url' => null, 'ssl' => false, 'timeout' => 0.01)
            ))
        ), $config);
    }

    public function testSingleTopLevelClientRenamedConnectionDefinition()
    {
        $config = $this->processor->processConfiguration($this->config, array(
            array(
                'client' => array('default_connection' => 'test'),
            )
        ));

        $this->assertEquals(array(
            'client' => array('default_connection' => 'test', 'connections' => array(
                'test' => array('host' => 'localhost', 'port' => 5984, 'user' => null, 'password' => null, 'ip' => null, 'logging' => false, 'url' => null, 'ssl' => false, 'timeout' => 0.01)
            ))
        ), $config);
    }

    public function testMultipleClientConnections()
    {
        $config = $this->processor->processConfiguration($this->config, array(
            array(
                'client' => array('default_connection' => 'test', 'connections' => array(
                    'test' => array('port' => 4000),
                    'test2' => array('port' => 1984),
                )),
            )
        ));

        $this->assertEquals(array(
            'client' => array('default_connection' => 'test', 'connections' => array(
                'test' => array('port' => 4000, 'host' => 'localhost', 'user' => null, 'password' => null, 'ip' => null, 'logging' => false, 'url' => null, 'ssl' => false, 'timeout' => 0.01),
                'test2' => array('port' => 1984, 'host' => 'localhost', 'user' => null, 'password' => null, 'ip' => null, 'logging' => false, 'url' => null, 'ssl' => false, 'timeout' => 0.01)
            ))
        ), $config);
    }

    public function testSingleTopLevelDocumentManager()
    {
        $config = $this->processor->processConfiguration($this->config, array(
            array(
                'client' => array(),
                'odm' => array()
            )
        ));

        $this->assertEquals(array(
            'client' => array('default_connection' => 'default', 'connections' => array(
                'default' => array('host' => 'localhost', 'port' => 5984, 'user' => null, 'password' => null, 'ip' => null, 'logging' => false, 'url' => null, 'ssl' => false, 'timeout' => 0.01)
            )),
            'odm' => array(
                'default_document_manager' => 'default',
                'document_managers' => array(
                    'default' => array(
                        'metadata_cache_driver' => array('type' => 'array', 'namespace' => null),
                        'auto_mapping' => false,
                        'mappings' => array(),
                        'design_documents' => array(),
                        'lucene_handler_name' => false,
                        'uuid_buffer_size' => 20,
                        'view_name' => 'symfony',
                        'all_or_nothing_flush' => true,
                    ),
                ),
                'auto_generate_proxy_classes' => false,
                'proxy_dir' => '%kernel.cache_dir%/doctrine/CouchDBProxies',
                'proxy_namespace' => 'CouchDBProxies',
            )
        ), $config);
    }

    public function testMultipleDocumentManager()
    {
        $config = $this->processor->processConfiguration($this->config, array(
            array(
                'client' => array(),
                'odm' => array(
                    'default_document_manager' => 'test',
                    'document_managers' => array('test' => array('connection' => 'default'), 'test2' => array())
                )
            )
        ));

        $this->assertEquals(array(
            'client' => array('default_connection' => 'default', 'connections' => array(
                'default' => array('host' => 'localhost', 'port' => 5984, 'user' => null, 'password' => null, 'ip' => null, 'logging' => false, 'url' => null, 'ssl' => false, 'timeout' => 0.01)
            )),
            'odm' => array(
                'default_document_manager' => 'test',
                'document_managers' => array(
                    'test' => array(
                        'connection' => 'default',
                        'metadata_cache_driver' => array('type' => 'array', 'namespace' => null),
                        'auto_mapping' => false,
                        'mappings' => array(),
                        'design_documents' => array(),
                        'lucene_handler_name' => false,
                        'uuid_buffer_size' => 20,
                        'view_name' => 'symfony',
                        'all_or_nothing_flush' => true,
                    ),
                    'test2' => array(
                        'metadata_cache_driver' => array('type' => 'array', 'namespace' => null),
                        'auto_mapping' => false,
                        'mappings' => array(),
                        'design_documents' => array(),
                        'lucene_handler_name' => false,
                        'uuid_buffer_size' => 20,
                        'view_name' => 'symfony',
                        'all_or_nothing_flush' => true,
                    ),
                ),
                'auto_generate_proxy_classes' => false,
                'proxy_dir' => '%kernel.cache_dir%/doctrine/CouchDBProxies',
                'proxy_namespace' => 'CouchDBProxies',
            )
        ), $config);
    }
}
