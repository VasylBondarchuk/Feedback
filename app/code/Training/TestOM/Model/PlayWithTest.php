<?php

namespace Training\TestOM\Model;

class PlayWithTest
{
    private $testObject;
    private $testObjectFactory;
    private $manager;

    public function __construct(
        \Training\TestOM\Model\Test $testObject,
        \Training\TestOM\Model\TestFactory $testObjectFactory,
        \Training\TestOM\Model\ManagerCustomImplementation $manager
    ) {
        $this->testObject = $testObject;
        $this->testObjectFactory = $testObjectFactory;
        $this->manager = $manager;
    }
    public function run()
    {
        echo "<b>test object with constructor arguments managed by di.xml.</b></br></br>";
        // test object with constructor arguments managed by di.xml
        $this->testObject->log();
        // test object with custom constructor arguments
        // some arguments are defined here, others - from di.xml
        $customArrayList = ['item1' => 'aaaaa', 'item2' => 'bbbbb'];
        $newTestObject = $this->testObjectFactory->create([
            'arrayList' => $customArrayList,
            'manager' => $this->manager
        ]);
        echo "</br></br>";
        echo "<b>some arguments are defined here, others - from di.xml.</b></br></br>";
        $newTestObject->log();
    }
}
