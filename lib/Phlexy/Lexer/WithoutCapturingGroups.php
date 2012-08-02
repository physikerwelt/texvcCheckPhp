<?php

class Phlexy_Lexer_WithoutCapturingGroups implements Phlexy_Lexer {
    protected $regex;
    protected $offsetToToken;

    public function __construct(array $regexToToken) {
        $lexerDataGenerator = new Phlexy_LexerDataGenerator;

        $this->regex = $lexerDataGenerator->getAllRegexesCompiledIntoOne(array_keys($regexToToken));

        $this->offsetToToken = array_values($regexToToken);
    }

    public function lex($string) {
        $tokens = array();

        $offset = 0;
        $line = 1;
        while (isset($string[$offset])) {
            if (!preg_match($this->regex, $string, $matches, 0, $offset)) {
                throw new Phlexy_LexingException(sprintf('Unexpected character "%s"', $string[$offset]));
            }

            // find the first non-empty element (but skipping $matches[0]) using a quick for loop
            for ($i = 1; '' === $matches[$i]; ++$i);

            $tokens[] = array($matches[0], $this->offsetToToken[$i - 1]);

            $offset += strlen($matches[0]);
            $line += substr_count("\n", $matches[0]);
        }

        return $tokens;
    }
}