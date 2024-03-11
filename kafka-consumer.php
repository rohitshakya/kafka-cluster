<?php
require_once 'vendor/autoload.php';

// Set up Kafka consumer
$conf = new \RdKafka\Conf();
//$conf->set('bootstrap.servers', '172.17.0.4:9092'); // Kafka container's IP address and port
$conf->set('bootstrap.servers', '0.0.0.0:9093'); // Kafka container's IP address and port

$conf->set('group.id', 'my_consumer_group');
$consumer = new \RdKafka\Consumer($conf);
$topic = $consumer->newTopic('my_topic');
$topicConf = new \RdKafka\TopicConf();
$topicConf->set('enable.auto.commit', 'true');
$topicConf->set('auto.commit.interval.ms', (string) 100);
$topicConf->set('auto.offset.reset', 'earliest');
$topic->consumeStart(0, RD_KAFKA_OFFSET_END); // Start consuming from the end of the topic

// Consume messages from Kafka topic
while (true) {
    
    $message = $topic->consume(0, 1000); // Poll for messages every second
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            echo "Received message:\n";
            echo "Payload: {$message->payload}\n";
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            echo "No more messages from partition\n";
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out\n";
            break;
        default:
            echo "Error: {$message->errstr()}\n";
            break;
    }
}
