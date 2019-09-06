<?php

namespace MilkyThinking\Trie;

class TrieNode
{
    /**
     * Node value.
     *
     * @var string|null
     */
    private $value;

    /**
     * Child nodes.
     *
     * @var array
     */
    private $children = [];

    public function __construct($value = null)
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
     * Set current node value.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Unset current node value.
     *
     * @return $this
     */
    public function unsetValue()
    {
        $this->value = null;

        return $this;
    }

    /**
     * Check if current node has value.
     *
     * @return bool
     */
    public function hasValue()
    {
        return !is_null($this->value);
    }

    /**
     * Check if child node exists with value.
     *
     * @param string $value
     *
     * @return bool
     */
    public function hasChild($value)
    {
        return isset($this->children[$value]);
    }

    /**
     * Check if current node has children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * Get child node with value.
     *
     * @param string $value
     *
     * @return \MilkyThinking\Trie\TrieNode
     */
    public function getChild($value)
    {
        if (!$this->hasChild($value)) {
            throw new \RuntimeException("$value not found.");
        }

        return $this->children[$value];
    }

    /**
     * Add child node with value.
     *
     * @param string $value
     * @param bool $nullable
     *
     * @return self
     */
    public function addChild($value, $nullable = true)
    {
        if ($nullable) {
            $this->children[$value] = new TrieNode();
        } else {
            $this->children[$value] = new TrieNode($value);
        }

        return $this;
    }

    /**
     * Remove child node with value.
     *
     * @param $value
     *
     * @return self
     */
    public function removeChild($value)
    {
        unset($this->children[$value]);

        return $this;
    }
}
