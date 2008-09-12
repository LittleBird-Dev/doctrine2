<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

#namespace Doctrine::ORM::Internal;

/**
 * The CommitOrderCalculator is used by the UnitOfWork to sort out the
 * correct order in which changes to entities need to be persisted.
 *
 * @since 2.0
 * @todo Rename to: CommitOrderCalculator
 * @author Roman Borschel <roman@code-factory.org> 
 */
class Doctrine_Internal_CommitOrderCalculator
{
    private $_currentTime;
    
    /**
     * The node list used for sorting.
     *
     * @var array
     */
    private $_nodes = array();
    
    /**
     * The topologically sorted list of items. Note that these are not nodes
     * but the wrapped items.
     *
     * @var array
     */
    private $_sorted;
    
    /**
     * Orders the given list of CommitOrderNodes based on their dependencies.
     * 
     * Uses a depth-first search (DFS) to traverse the graph.
     * The desired topological sorting is the reverse postorder of these searches.
     *
     * @param array $nodes  The list of (unordered) CommitOrderNodes.
     * @return array  The list of ordered items. These are the items wrapped in the nodes.
     */
    public function getCommitOrder()
    {
        // Check whether we need to do anything. 0 or 1 node is easy.
        $nodeCount = count($this->_nodes);
        if ($nodeCount == 0) {
            return array();
        } else if ($nodeCount == 1) {
            $node = array_pop($this->_nodes);
            return array($node->getClass());
        }
        
        $this->_sorted = array();
        
        // Init
        foreach ($this->_nodes as $node) {
            $node->markNotVisited();
            $node->setPredecessor(null);
        }
        
        $this->_currentTime = 0;
        
        // Go
        foreach ($this->_nodes as $node) {
            if ($node->isNotVisited()) {
                $node->visit();
            }
        }
        
        return $this->_sorted;
    }
    
    public function addNode($key, $node)
    {
        $this->_nodes[$key] = $node;
    }
    
    public function addNodeWithItem($key, $item)
    {
        $this->_nodes[$key] = new Doctrine_Internal_CommitOrderNode($item, $this);
    }
    
    public function getNodeForKey($key)
    {
        return $this->_nodes[$key];
    }
    
    public function hasNodeWithKey($key)
    {
        return isset($this->_nodes[$key]);
    }
    
    public function clear()
    {
        $this->_nodes = array();
        $this->_sorted = array();
    }
    
    
    public function getNextTime()
    {
        return ++$this->_currentTime;
    }
    
    public function prependNode($node)
    {
        array_unshift($this->_sorted, $node->getClass());
    }
}

?>