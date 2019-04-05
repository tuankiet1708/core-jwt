<?php

namespace Leo\JWT;

interface Token {
    public function buildToken();
    public function parse($token);
    public function checkValid($token);
    public function info();
}