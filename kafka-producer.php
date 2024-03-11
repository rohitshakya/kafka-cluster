<?php
require_once 'vendor/autoload.php';

// Set up Kafka producer
$conf = new \RdKafka\Conf();
$conf->set('bootstrap.servers', '0.0.0.0:9093'); // Use Kafka container's IP address

//$conf->set('bootstrap.servers', '0.0.0.0:9092'); // Use Kafka container's IP address
$conf->set('socket.timeout.ms', 6000); 
$producer = new \RdKafka\Producer($conf);
$topic = $producer->newTopic('my_topic');
$counter=0;
// Send message to Kafka topic every second
while (true) {
    $message = 'Hello Kafka! '.$counter;
    $counter++;
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
    echo "Message queue length: " . $producer->getOutQLen() . PHP_EOL;
    $result = $producer->flush(1000);
    if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
        // Handle error
        echo "Error flushing messages: " . rd_kafka_err2str($result) . "\n";
    }
    echo "Message queue length before publishing: " . $producer->getOutQLen() . PHP_EOL;

    echo "Message sent to Kafka topic.\n";
    sleep(1); // Pause execution for one second
}
?>
