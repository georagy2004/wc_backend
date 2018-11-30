<?php

include_once "wxBizDataCrypt.php";


$appid = 'wxfbd55caafb2163f1';
$sessionKey = 'ffsAPkhW9OrlnnlQzzrVuQ==';

$encryptedData="hZF2KarQLj5uS4CMgzd5THuS/z9ONExxciIVwgMqyc+KzT9Ckr+Re/oobdJ+C4mGoSYy1kXTfmKL6UC7JYaQ7RrJuF1ygdjGbO6j7r9FTxiLIsoHjENQrqobbw2ReL+U/aEpZZ8HWeDeQ/dWrrw5Xf40zUe/zrLc+OYB2lzh5ZjgUD8QhMphf3l5vY83c2V7cTT4WZ7ORThUwdfCHMyF9bUK0O1N+cEgd2WgvdD1Qz4bryKXXKyyYDNXayiUzR2y9FeB7hxG9Qk5cFjNEMgXGju9DRveneLpR+zVRL2DolLHUfN6YJ8TO/PHbWOoVEJgWhPKCv/Bkibo4FU7cD3s2Z5hq5cP0ztjUjfZtXdy7HgP0akVjrvMhCoxZ+DYVW9bM1PFLUTbBqJk34PP7UpsTae51xzIiHPRxFueu/PVu40Tr3v0b678VCWabbfu2nxLmtpr+EZrgQ+si6nuIeWiAn4QXGwT3DiuUVlU6VSk3rc=";

$iv = "HS2yAWYpVZ1PFvs/guw98w==";

$pc = new WXBizDataCrypt($appid, $sessionKey);
$errCode = $pc->decryptData($encryptedData, $iv, $data );

if ($errCode == 0) {
    print($data . "\n");
} else {
    print($errCode . "\n");
}
