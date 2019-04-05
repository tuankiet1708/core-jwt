<?php

namespace Leo\JWT;

use Leo\JWT\Token;
use Leo\JWT\TokenException;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;

class JWT implements Token {
    const AUDIENCE = 'apis';
    
    /**
     * load configuration from the default configuration
     */
    protected $config = [];

    /**
     * Create a new JWT instance.
     *
     * @param array|null $config You can create a new instance with your owned configuration, formatted as config/config.php
     * @return void
     */
    public function __construct($config = null) {
        $this->config = array_merge(
            require(__DIR__.'/../config/config.php'),
            (array) $config
        );
    }

    /**
     * Build owned token
     * 
     * @return string
     */
    public function buildToken() {
        $info = $this->info();
        $time = time();

        $signer = new Sha256();
        $token = (new Builder())
                        ->setIssuer($info['issuer']) // Configures the issuer (iss claim)
                        ->setAudience(self::AUDIENCE) // Configures the audience (aud claim)
                        ->setId($info['app_id'], true) // Configures the id (jti claim), replicating as a header item
                        ->setIssuedAt($time) // Configures the time that the token was issued (iat claim)
                        ->setNotBefore($time) // Configures the time that the token can be used (nbf claim)
                        ->setExpiration(time() + $info['ttl']) // Configures the expiration time of the token (exp claim)
                        ->set('uid', $info['app_id']) // Configures a new claim, called "uid"
                        ->sign($signer, $info['secret']) // creates a signature using "testing" as key
                        ->getToken(); // Retrieves the generated token
        
        return strval($token);
    }

    /**
     * Check if token is valid
     * 
     * @param string $token
     * @return bool
     */
    public function checkValid($token) {
        $token = $this->parse($token);

        // Get claims of token
        $claims = $this->getClaims($token);
        
        // Get party info
        $party = $this->party($claims['jti']);

        $this->verify($token, $party);

        $this->validate($token, $party);

        return true;
    }

    /**
     * Verify party token
     * 
     * @param \Lcobucci\JWT\Token $token
     * @param array $party
     * @return bool
     * @throws App\Api\TokenException
     */
    protected function verify($token, $party) {
        $signer = new Sha256();
        $result = $token->verify($signer, $party['secret']);

        if (!$result) throw new TokenException('Invalid Signature');
        return true;
    }

    /**
     * Validate party token
     * 
     * @param \Lcobucci\JWT\Token $token
     * @param array $party
     * @return bool
     * @throws App\Api\TokenException
     */
    protected function validate($token, $party) {
        $data = new ValidationData();

        if ( $this->timeLimitExceed($token, $party) ) {
            throw new TokenException('Time limit exceed');
        }

        if ( $this->isFuture($token, $party) ) {
            throw new TokenException('Issued at (iat) timestamp cannot be in the future');
        }

        if ( $this->isPast($token, $party) ) {
            throw new TokenException('Token has expired');
        }

        if ( ! $this->canUse($token, $party) ) {
            throw new TokenException('Token can be only used after (nbf)');
        }

        $data->setAudience(self::AUDIENCE);
        if ( ! $token->validate($data) ) {
            throw new TokenException('Invalid audience (aud)');
        }

        $data->setId($party['app_id']);
        if ( ! $token->validate($data) ) {
            throw new TokenException('Invalid id (jti)');
        }

        return true;
    }

    /**
     * Time Limit Exceed
     * 
     * @param \Lcobucci\JWT\Token $token
     * @param array $party
     * @return bool
     */
    protected function timeLimitExceed($token, $party) {
        return ( time() - $token->getClaim('iat') ) > intval($party['ttl']);
    }
    
    /**
     * Is future
     * 
     * @param \Lcobucci\JWT\Token $token
     * @param array $party
     * @return bool
     */
    protected function isFuture($token, $party) {
        return ($token->getClaim('iat') > time());
    }

    /**
     * Is past
     * 
     * @param \Lcobucci\JWT\Token $token
     * @param array $party
     * @return bool
     */
    protected function isPast($token, $party) {
        return ($token->getClaim('exp') < time());
    }

    /**
     * Token can be used or not
     * 
     * @param \Lcobucci\JWT\Token $token
     * @param array $party
     * @return bool
     */
    protected function canUse($token, $party) {
        return ($token->getClaim('nbf') <= time());
    }

    /**
     * Parse token from a string
     * 
     * @param string $token
     * @return \Lcobucci\JWT\Token
     */
    public function parse($token) {
        return (new Parser())->parse($token);
    }

    /**
     * Get party info
     * 
     * @param string $party
     * @return array
     */
    public function party($party) {
        $party = array_get($this->config, "jwt.parties.{$party}", []);

        list($app_id, $url, $secret, $ttl) = $party;

        return compact('app_id', 'url', 'secret', 'ttl');
    }

    /**
     * Get token claims
     * 
     * @param string|\Lcobucci\JWT\Token $token
     * @return array
     */
    public function getClaims($token) {
        if (empty($token)) {
            throw new TokenException('Token required');
        }
        
        if (! $token instanceof \Lcobucci\JWT\Token) {
            $token = $this->parse($token);
        }
        // the issuer
        $iss = $token->getClaim('iss');
        // the audience
        $aud = $token->getClaim('aud');
        // the id
        $jti = $token->getHeader('jti');
        // the time that the token was issued
        $iat = $token->getClaim('iat');
        // the time that the token can be used
        $nbf = $token->getClaim('nbf');
        // Configures the expiration time of the token
        $exp = $token->getClaim('exp');
        // uid claim
        $uid = $token->getClaim('uid');

        return compact('iss', 'aud', 'jti', 'uid', 'iat', 'nbf', 'exp');
    }

    /**
     * Get owned info
     * 
     * @return array
     */
    public function info() {
        return [
            'issuer' => str_slug(array_get($this->config, 'jwt.app_name'), '_'),
            'app_id' => array_get($this->config, 'jwt.app_id'),
            'secret' => array_get($this->config, 'jwt.secret'),
            'ttl' => array_get($this->config, 'jwt.ttl'),
        ];
    }
}