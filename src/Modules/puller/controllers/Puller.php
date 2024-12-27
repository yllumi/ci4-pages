<?php

class Puller extends MY_Controller {

    public function index()
    {
        echo setting_item('puller.enable') == '1' ? "Puller enabled" : "Puller disabled";
    }

    public function run($project = 'self')
    {
        // Get repository list
        $repos = explode("\n", setting_item('puller.repos'));
        if(!$repos) die('Repo not set');

        $repoArray = [];
        foreach ($repos as $repo) {
            $temp = explode(" ", $repo);
            $repoArray[$temp[0]]['path'] = $temp[1];
            $repoArray[$temp[0]]['branch'] = trim($temp[2]) ?? "";
        }
        if (!isset($repoArray[$project])) die("Repo name not registered.");


        // Use Github Webhook
        if (setting_item('puller.secret_key')) {
            // Get github payload
            $payload = file_get_contents('php://input');
            if(!$payload) die('Payload unavailable.');
            $payloadArr = json_decode($payload, true);
            $ref = explode('/', $payloadArr['ref']);
            $branchPushed = trim($ref[2]);

            // Check signature
            $secret = setting_item('puller.secret_key');
            $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);
            if (!hash_equals($hash, $_SERVER['HTTP_X_HUB_SIGNATURE_256'])) die("Signature didn't match.");
            if ($repoArray[$project]['branch'] != $branchPushed) die("Pushed branch ($branchPushed) is not the target for pulling ({$repoArray[$project]['branch']}).");
        }

        if(!file_exists(ROOTPATH . "writable/pullthis")) mkdir(ROOTPATH . "writable/pullthis", 0755, true);
        if(!file_exists(ROOTPATH . "writable/logs")) mkdir(ROOTPATH . "writable/logs", 0755, true);

        if(! is_writable(ROOTPATH . "writable/pullthis")) die("Unable to open file.");

        if(! file_exists($repoArray[$project]['path'].'/.git')) die("Repo folder not exist.");

        $content = "{$repoArray[$project]['path']}|{$repoArray[$project]['branch']}\n";
        file_put_contents(ROOTPATH . "writable/pullthis/" . $project, $content);
        echo "$project is on the way to be pulled.";
    }

}
