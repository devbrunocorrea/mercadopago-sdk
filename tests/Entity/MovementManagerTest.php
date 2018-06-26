<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk
 * Created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file
 * LICENSE which is distributed with this source code.
 * Para a informação dos direitos autorais e de licença você deve ler o arquivo
 * LICENSE que é distribuído com este código-fonte.
 * Para obtener la información de los derechos de autor y la licencia debe leer
 * el archivo LICENSE que se distribuye con el código fuente.
 * For more information, see <https://opensource.gpupo.com/>.
 *
 */

namespace  Gpupo\MercadopagoSdk\Tests\Entity;

use Gpupo\Common\Entity\Collection;
use  Gpupo\CommonSchema\ArrayCollection\Trading\Payment\Payment;
use Gpupo\MercadopagoSdk\Tests\TestCaseAbstract;
use Symfony\Component\Yaml\Yaml;

/**
 * @coversDefaultClass \Gpupo\MercadopagoSdk\Entity\MovementManager
 */
class MovementManagerTest extends TestCaseAbstract
{
    public function testGetBalance()
    {
        $manager = $this->mockupManager('mockup/Movement/balance.yaml');
        $balance = $manager->getBalance();
        $this->assertInstanceOf(Collection::class, $balance);
        $this->assertSame(4350.87, $balance->getAvailableBalance());
        $this->assertSame(7987.58, $balance->getUnavailableBalance());
        $this->assertSame(12338.45, $balance->getTotalAmount());
    }

    public function testFindPaymentById()
    {
        $manager = $this->mockupManager('mockup/Movement/payment.yaml');
        $payment = $manager->findPaymentById(5046323112);
        $raw = $payment->getExpands();
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame(5046323112, $payment->getPaymentNumber(), 'Payment Number');
        $this->assertSame($raw['transaction_details']['net_received_amount'], $payment->getTransactionNetAmount(), 'Detail net');
        $this->assertSame($raw['transaction_details']['total_paid_amount'], $payment->getTotalPaidAmount(), 'Detail paid');
        $this->assertSame('BRL', $payment->getCurrencyId(), 'currency');
        $this->assertSame(254289619, $payment->getCollector()->getIdentifier(), 'Collector ID');
        $this->assertSame(0.0, $payment->getOverpaidAmount());

        // file_put_contents('var/cache/payment.yaml', Yaml::dump($payment->toArray(), 4, 4));
        //dump($payment);
    }

    protected function mockupManager($file)
    {
        $data = $this->getResourceYaml($file);
        $manager = $this->getFactory()->factoryManager('movement');
        $response = $this->factoryResponseFromArray($data);
        $manager->setDryRun($response);

        return $manager;
    }
}
