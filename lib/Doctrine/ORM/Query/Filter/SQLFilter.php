<?php
/*
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
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\ORM\Query\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\EntityManager;

/**
 * The base class that user defined filters should extend.
 *
 * Handles the setting and escaping of parameters.
 *
 * @author Alexander <iam.asm89@gmail.com>
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @abstract
 */
abstract class SQLFilter
{
    /**
     * The entity manager.
     * @var EntityManager
     */
    private $em;

    /**
     * Parameters for the filter.
     * @var array
     */
    private $parameters;

    /**
     * Constructs the SQLFilter object.
     *
     * @param EntityManager $em The EM
     */
    final public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Sets a parameter that can be used by the filter.
     *
     * @param string $name Name of the parameter.
     * @param string $value Value of the parameter.
     * @param string $type Type of the parameter.
     *
     * @return SQLFilter The current SQL filter.
     */
    final public function setParameter($name, $value, $type)
    {
        // @todo: check for a valid type?
        $this->parameters[$name] = array('value' => $value, 'type' => $type);

        // Keep the parameters sorted for the hash
        ksort($this->parameters);

        // The filter collection of the EM is now dirty
        $this->em->getFilters()->setFiltersStateDirty();

        return $this;
    }

    /**
     * Gets a parameter to use in a query.
     *
     * The function is responsible for the right output escaping to use the
     * value in a query.
     *
     * @param string $name Name of the parameter.
     *
     * @return string The SQL escaped parameter to use in a query.
     */
    final public function getParameter($name)
    {
        if (!isset($this->parameters[$name])) {
            throw new \InvalidArgumentException("Parameter '" . $name . "' does not exist.");
        }

        return $this->em->getConnection()->quote($this->parameters[$name]['value'], $this->parameters[$name]['type']);
    }

    /**
     * Returns as string representation of the SQLFilter parameters (the state).
     *
     * @return string String representation of the SQLFilter.
     */
    final public function __toString()
    {
        return serialize($this->parameters);
    }

    /**
     * Gets the SQL query part to add to a query.
     *
     * @return string The constraint SQL if there is available, empty string otherwise
     */
    abstract public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias);
}