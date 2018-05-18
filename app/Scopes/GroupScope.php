<?php

namespace SET\Scopes;

use Adldap\Query\Builder;
use Adldap\Laravel\Scopes\ScopeInterface;

class GroupScope implements ScopeInterface
{
    /**
     * Apply the scope to a given LDAP query builder.
     *
     * @param Builder $query
     *
     * @return void
     */
    public function apply(Builder $query)
    {
        // The distinguished name of our LDAP group.
        $ldapGroup = config('app.ldap_group');

        $limitationFilter = config('app.limitation_filter');

        if ($limitationFilter != '') {
                $query->in($ldapGroup)->whereDivision($limitationFilter);
        } else {
                $query->in($ldapGroup);
        }
    }
}
