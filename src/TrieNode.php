<?php

namespace MilkyThinking\Trie;

class TrieNode
{
    /**
     * Node value.
     *
     * @var string
     */
    private $value;

    /**
     * Child nodes.
     *
     * @var array
     */
    private $children = [];

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get current node value.
     *
     * @return string
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Check if current node is leaf.
     *
     * @return bool
     */
    public function isLeaf()
    {
        return empty($this->children);
    }

    /**
     * Check if child node exists with value.
     *
     * @param string $value
     *
     * @return bool
     */
    public function has($value)
    {
        return isset($this->children[$value]);
    }

    /**
     * Get child node with value.
     *
     * @param string $value
     *
     * @return \MilkyThinking\Trie\TrieNode
     */
    public function get($value)
    {
        if (!$this->has($value)) {
            throw new \RuntimeException("$value not found.");
        }

        return $this->children[$value];
    }

    /**
     * Add child node with value.
     *
     * @param $value
     *
     * @return void
     */
    public function add($value)
    {
        $this->children[$value] = new TrieNode($value);
    }

    /**
     * Remove child node with value.
     *
     * @param $value
     *
     * @return void
     */
    public function remove($value)
    {
        unset($this->children[$value]);
    }
}
