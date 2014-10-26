<?php
namespace info\synapp\tools\captcha;

use \Exception;
use \info\synapp\tools\passwordgenerator\cryptosecureprng\cryptosecureprng;

class captchaword {

    /**
     * @var array $symbols
     */
    private $defaultSymbols;

    /**
     * @var array $defaultTrie
     */
    private $defaultTrie;

    /**
     * @var int $length
     */
    private $defaultLength;

    /**
     * @var int $prng
     */
    private $prng;

    /**
     * @param array|string $validSymbols
     * @return array
     * @throws Exception
     */
    private function generateTrie(&$validSymbols){

        if (is_string($validSymbols)){
            $validSymbols = array($validSymbols);
        }
        if (!is_array($validSymbols)){
            throw new Exception(
                "Invalid input argument for \$dictionary (must be array)",
                500
            );
        }

        $trie = array();

        $dictionaryCount = count($validSymbols);
        $f = false;
        for ($i=0;$i<$dictionaryCount;$i++){
            $word = $validSymbols[$i];
            if ($f&&!inTrie('in',$trie)){
                var_export($trie);
                exit;
            }
            if (!is_string($word)){
                throw new Exception(
                    "Invalid input argument for \$word (must be string)",
                    500
                );
            }
            $wordLength = strlen($word);
            $subTrie = &$trie;
            for ($j=1;$j<$wordLength;$j++){
                if (array_key_exists($subWord = substr($word,0,$j),$subTrie)){
                    $subTrie = &$subTrie[$subWord];
                }
            }
            if (array_key_exists($word,$subTrie)){
                continue;
            }
            $keys = array_keys($subTrie);
            if (!array_key_exists($word,$subTrie)) {
                $subTrie[$word] = array();
            }
            foreach ($keys as $testWordForPrefix){
                if (substr($testWordForPrefix,0,$wordLength) === $word){
                    $subTrie[$word][$testWordForPrefix] = &$subTrie[$testWordForPrefix];
                    unset($subTrie[$testWordForPrefix]);
                }
            }
        }

        return $trie;

    }
    
    /**
     * @param null|string|array $symbols
     * @param null|int $length
     * @param string $separator
     * @throws \Exception
     * @return string
     */
    public function generateWord($symbols = null, $length = null, $separator = '   '){
        
        if ($symbols===null){
            $symbols = $this->defaultSymbols;
        }
        
        if (is_string($symbols)&&strlen($symbols)>0){
            $symbols = str_split($symbols);
        }
        if (!is_array($symbols)){
            throw new Exception(
                'Invalid $symbols.',
                500
            );
        }
        
        if ($length === null){
            $length = $this->defaultLength;
        } else if (!is_numeric($length)){
            throw new Exception(
                'Length is not numeric',
                500
            );
        }
        
        $word = '';
        $lastIndexOfSymbols = count($symbols)-1;
        for ($i = 0; $i <= $length-1; $i++) { /* create the word */
            $word .= $separator.$this->defaultSymbols[mt_rand(0, $lastIndexOfSymbols)];
            error_log(var_export($word,true));
        }
        
        return ltrim($word,$separator);
        
    }

    /**
     * Checks if word is on dictionary trie
     *
     * @param string $word
     * @param array $trie
     * @return bool
     */
    private function inTrie($word, &$trie){

        $wordLen = strlen($word);
        $node = &$trie;
        $found = false;
        for ($i=1;$i<=$wordLen;$i++){
            $index = substr($word,0,$i);
            if (isset($node[$index])){
                $node = &$node[$index];
                $found = true;
            } else {
                $found = false;
            }
        }

        return $found;

    }
    
    /**
     * Checks if a $word is a concatenation of valid $symbols
     * E.g. `$word = 'paw'`, 
     * `$this->generateTrie($symbols = array('p', 'pa', 'aw'))`
     * would return true, because `$word = 'p'.'aw'`
     *
     * @param string $word
     * @param array $trie
     * @return bool
     */
    private function isValidWord($word,&$trie){
        $nodes = array($word);
        while (count($nodes)>0){
            $node = array_shift($nodes);
            if ($this->inTrie($node,$trie)) { return true; }
            $nodeExpansions = array();
            $nodeLength = strlen($node);
            for ($len=$nodeLength-1;$len>0;$len--){
                if ($this->inTrie(substr($node, 0, $len), $trie)){
                    $nodeExpansions[] = substr($node, $len-$nodeLength);
                }
            }
            $nodes = array_merge($nodeExpansions,$nodes);
        }
        return false;
    }

    /**
     * Checks a word contains only valid symbols
     * 
     * @param string $word
     * @param null|array|string $symbols
     * @throws \Exception
     * @return bool
     */
    public function validateWord($word, $symbols = null){
        
        if ($this->defaultTrie === null){
            $this->defaultTrie = $this->generateTrie($this->defaultSymbols);
        }
        
        if (!is_string($word)) {
            return false;
        }
        
        if ($symbols===null){
            
            $symbols = &$this->defaultSymbols;
            $trie = &$this->defaultTrie;
            
        } else {
        
            if (is_string($symbols)&&strlen($symbols)>0){
                $symbols = str_split($symbols);
            }
            $trie = $this->generateTrie($symbols);
            
        }
        
        if (!is_array($symbols)||count($symbols)<=0){
            throw new Exception(
                'Invalid $symbols.',
                500
            );
        }
        
        return $this->isValidWord($word,$trie);
        
    }

    /**
     * Sets default parameters for captcha word generation and validation
     *
     * @param null|array|string|bool $defaultInternalOrExternalSymbols
     * @param null|int $defaultLength
     * @param bool $useExternalSymbolsArray Use external symbols array defined
     * as $validSymbols array of strings stored in top10000.php (defaults to
     * true)
     * @param $prng
     */
    public function __construct(
        $defaultInternalOrExternalSymbols = null, 
        $defaultLength = null, 
        $useExternalSymbolsArray = true,
        $prng = null
    ){
        if ($prng === null){
            $this->prng = new cryptosecureprng();
        } else {
            $this->prng = $prng;
        }
        if (is_array($defaultInternalOrExternalSymbols)){
            $this->defaultSymbols = $defaultInternalOrExternalSymbols;
        } else if (
            is_string($defaultInternalOrExternalSymbols)
            && strlen($defaultInternalOrExternalSymbols)>0
        ){
            $this->defaultSymbols = str_split($defaultInternalOrExternalSymbols);
        } else if ($defaultInternalOrExternalSymbols === null){
            include 'top10000.php';
            $this->defaultSymbols = &$validSymbols;
            if (is_numeric($defaultLength)){
                $this->defaultLength = $defaultLength;
            } else {
                $this->defaultLength = 2;
            }
        } else {
            $this->defaultSymbols = array(
                '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'C', 'E', 'G', 
                'H', 'K', 'M', 'N', 'P', 'R', 'S', 'U', 'V', 'W', 'Z', 'Y', 'Z'
            );
            if (is_numeric($defaultLength)){
                $this->defaultLength = $defaultLength;
            } else {
                $this->defaultLength = 5;
            }
        }
        
    }
    
}