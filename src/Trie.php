<?php

namespace MilkyThinking\Trie;

class Trie
{
    /**
     * @var string
     */
    private $encoding;

    /**
     * @var \MilkyThinking\Trie\TrieNode
     */
    private $rootNode;

    public function __construct($encoding = 'UTF-8')
    {
        $this->encoding = $encoding;
        $this->rootNode = new TrieNode('');
    }

    /**
     * Add word to trie.
     *
     * @param string $word
     *
     * @return void
     */
    public function add($word)
    {
        $length = $this->length($word);
        if (!$length) {
            return;
        }

        $currentNode = $this->rootNode;

        for ($i = 0; $i < $length; $i++) {
            $value = $this->valueOf($word, $i);
            if (!$currentNode->has($value)) {
                $currentNode->add($value);
            }

            $currentNode = $currentNode->get($value);
        }
    }

    /**
     * Add multiple words to trie.
     *
     * @param string[] $words
     *
     * @return void
     */
    public function addMultiple(array $words)
    {
        foreach ($words as $word) {
            $this->add($word);
        }
    }

    /**
     * Remove word from trie.
     *
     * @param string $word
     *
     * @return void;
     */
    public function remove($word)
    {
        $length = $this->length($word);
        if (!$length) {
            return;
        }

        $nodeStack = new \SplStack();

        $currentNode = $this->rootNode;
        for ($i = 0; $i < $length; $i++) {
            $value = $this->valueOf($word, $i);
            if (!$currentNode->has($value)) {
                return;
            }

            $currentNode = $currentNode->get($value);
            $nodeStack->push($currentNode);

            if ($i == $length - 1 && !$currentNode->isLeaf()) {
                return;
            }
        }

        /** @var \MilkyThinking\Trie\TrieNode $currentNode */
        $currentNode = $nodeStack->pop();
        $currentValue = $currentNode->value();

        while (!$nodeStack->isEmpty()) {
            $currentNode = $nodeStack->pop();
            $currentNode->remove($currentValue);

            $currentValue = $currentNode->value();
        }

        $this->rootNode->remove($currentValue);
    }

    /**
     * Remove multiple words from trie.
     *
     * @param string[] $words
     *
     * @return void;
     */
    public function removeMultiple($words)
    {
        foreach ($words as $word) {
            $this->remove($word);
        }
    }

    /**
     * Search matched words for content.
     *
     * @param string $content
     *
     * @return array [word1 => count1, word2 => count2]
     */
    public function search($content)
    {
        $length = $this->length($content);
        if (!$length) {
            return [];
        }

        $matchedWords = [];
        for ($i = 0; $i < $length; $i ++) {
            $currentNode = $this->rootNode;
            $matchedWord = '';
            for ($j = $i; $j < $length; $j ++) {
                $value = $this->valueOf($content, $j);
                if ($currentNode->has($value)) {
                    $matchedWord .= $value;
                    $currentNode = $currentNode->get($value);

                    if ($j == $length - 1 && $currentNode->isLeaf()) {
                        if (!isset($matchedWords[$matchedWord])) {
                            $matchedWords[$matchedWord] = 1;
                        } else {
                            $matchedWords[$matchedWord] += 1;
                        }
                    }

                    continue;
                } else {
                    if ($currentNode->isLeaf()) {
                        if (!isset($matchedWords[$matchedWord])) {
                            $matchedWords[$matchedWord] = 1;
                        } else {
                            $matchedWords[$matchedWord] += 1;
                        }
                    }

                    break;
                }
            }
        }

        return $matchedWords;
    }

    protected function length($content)
    {
        return mb_strlen($content, $this->encoding);
    }

    protected function valueOf($content, $index)
    {
        return mb_substr($content, $index, 1, $this->encoding);
    }
}
