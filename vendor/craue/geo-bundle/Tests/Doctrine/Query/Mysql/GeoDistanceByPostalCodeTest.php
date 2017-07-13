<?php

namespace Craue\GeoBundle\Tests\Doctrine\Query\Mysql;

use Craue\GeoBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class GeoDistanceByPostalCodeTest extends IntegrationTestCase {

	/**
	 * @dataProvider getPlatformConfigs
	 */
	public function testNoResults($platform, $config, $requiredExtension) {
		$this->initClient($requiredExtension, array('environment' => $platform, 'config' => $config));

		$result = $this->getPoisPerGeoDistanceByPostalCode('DE', '10551');

		$this->assertCount(0, $result);
	}

	/**
	 * @dataProvider getPlatformConfigs
	 */
	public function testDistance($platform, $config, $requiredExtension) {
		$this->initClient($requiredExtension, array('environment' => $platform, 'config' => $config));

		$this->persistGeoPostalCode('DE', '14473', 52.392759, 13.065135);
		$this->persistGeoPostalCode('DE', '10551', 52.525011, 13.369438);

		$result = $this->getPoisPerGeoDistanceByPostalCode('DE', '10551');

		$this->assertCount(2, $result);
		$this->assertEquals(0, $result[0]['distance']);
		$this->assertEquals(25.324980933453528, $result[1]['distance']);
	}

	/**
	 * @dataProvider getPlatformConfigs
	 */
	public function testUnknownPostalCode_withRadius($platform, $config, $requiredExtension) {
		$this->initClient($requiredExtension, array('environment' => $platform, 'config' => $config));

		$this->persistGeoPostalCode('DE', '14473', 52.392759, 13.065135);
		$this->persistGeoPostalCode('DE', '10551', 52.525011, 13.369438);

		$result = $this->getPoisPerGeoDistanceByPostalCode('DE', '20099', 1000.1);

		$this->assertCount(0, $result);
	}

	/**
	 * @dataProvider getPlatformConfigs
	 */
	public function testUnknownPostalCode_withoutRadius($platform, $config, $requiredExtension) {
		$this->initClient($requiredExtension, array('environment' => $platform, 'config' => $config));

		$this->persistGeoPostalCode('DE', '14473', 52.392759, 13.065135);
		$this->persistGeoPostalCode('DE', '10551', 52.525011, 13.369438);

		$result = $this->getPoisPerGeoDistanceByPostalCode('DE', '20099');

		$this->assertCount(2, $result);
		$this->assertNull($result[0]['distance']);
		$this->assertNull($result[1]['distance']);
	}

}
