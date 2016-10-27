<strong>Required Fields</strong>
<p>The following fields are required: First Name, Last Name, Email, and Username.</p>

<strong>Access Level</strong>
<p>Access level let's you set a security clearance that is different from the JPAS values. If not set, this value will be ignored and hidden on the user profile page.</p>
<p>If you set an access level, it will be used in the eligibility renewal value instead of the JPAS clearance value.</p>

<ul class="browser-default">
    <li>Secret has a renewal of every 10 years.</li>
    <li>Top Secret has a renewal of every 5 years.</li>
</ul>

<strong>JPAS Data</strong>
<p>The following data are written when a JPAS import is performed: Clearance, Eligibility Date, Investigation, Investigation Date.</p>

<strong>Closed Areas</strong>
<p>If a Group is flagged to have a closed area and is selected/deselected, then the corresponding closed area fields will display/hide.</p>

<strong>Status and deleting users</strong>
<p>
    When changing the status to something other than active, a destroyed date is set for the user. When the destroyed date is reached, the system will delete the user from the database.
    <ul class="browser-default">
        <li>Separated - Delete in 2 years.</li>
        <li>Destroyed - Delete at the start of next week.</li>
    </ul>
    Note: If a user is destroyed, but still in LDAP, they will be re-created with no records on the next login.
</p>
