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
        $this->rootNode = new TrieNode();
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

        for ($i = 0; $i < $length; $i ++) {
            $isLast = $length - 1 == $i;
            $value = $this->valueOf($word, $i);

            if ($currentNode->hasChild($value)) {
                if ($isLast) {
                    $currentNode->getChild($value)->setValue($value);
                }
            } else {
                if ($isLast) {
                    $currentNode->addChild($value, false);
                } else {
                    $currentNode->addChild($value);
                }
            }

            $currentNode = $currentNode->getChild($value);
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

        $nodeStack = [];

        $currentNode = $this->rootNode;
        array_push($nodeStack, [null, $this->rootNode]);

        for ($i = 0; $i < $length; $i ++) {
            $value = $this->valueOf($word, $i);
            if (!$currentNode->hasChild($value)) {
                $nodeStack = [];
                break;
            }

            $currentNode = $currentNode->getChild($value);
            array_push($nodeStack, [$value, $currentNode]);

            if ($i == $length - 1 && !$currentNode->hasValue()) {
                $nodeStack = [];
                break;
            }
        }

        if ($nodeStack) {
            $nodeStackCount = count($nodeStack);
            for ($i = $nodeStackCount - 1; $i >= 0; $i--) {
                /** @var \MilkyThinking\Trie\TrieNode $currentNode */
                list($currentValue, $currentNode) = $nodeStack[$i];
                if ($currentNode->hasValue() && $isFirst = ($i == $nodeStackCount- 1)) {
                    $currentNode->unsetValue();
                    if ($currentNode->hasChildren()) {
                        break;
                    }
                }

                if (isset($nodeStack[$i - 1])) {
                    /** @var \MilkyThinking\Trie\TrieNode $parentNode */
                    list(, $parentNode) = $nodeStack[$i - 1];
                    $parentNode->removeChild($currentValue);

                    if ($parentNode->hasChildren() || $parentNode->hasValue()) {
                        break;
                    }
                }
            }
        }
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
                if (!$currentNode->hasChild($value)) {
                    break;
                }

                $matchedWord .= $value;
                $currentNode = $currentNode->getChild($value);

                if ($currentNode->hasValue()) {
                    if (!isset($matchedWords[$matchedWord])) {
                        $matchedWords[$matchedWord] = 1;
                    } else {
                        $matchedWords[$matchedWord] += 1;
                    }
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
