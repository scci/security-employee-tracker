<strong>Required Fields</strong>
<p>The following fields are required: First Name, Last Name, Email, and Username.</p>

<strong>Access Level</strong>
<p>Access level let's you set a security clearance that is different from the JPAS values. If not set, this value will be ignored and hidden on the user profile page.</p>
<p>If you set an access level, it will be used in the eligibility renewal value instead of the JPAS clearance value.</p>

<ul class="browser-default">
    <li>Secret has a renewal of every 10 years.</li>
    <li>Top Secret has a renewal of every 6 years.</li>
</ul>

<p>If Continuous Evaluation is set to Yes, a Continuous Evaluation Date field will be displayed and a date must be selected.</p>
<p>If set, Continuous Evaluation Date will be used in the eligibility renewal value instead of the JPAS Investigation Date value.</p>

<strong>JPAS Data</strong>
<p>The following data are written when a JPAS import is performed: Clearance, Eligibility Date, Investigation, Investigation Date.</p>

<strong>Closed Areas</strong>
<p>If a Group is flagged to have a closed area and is selected/deselected, then the corresponding closed area fields will display/hide.</p>

<strong>Status</strong>
<p>
    When changing the status to something other than active, a user may be marked as separated and a separated date set for that user.
    <ul class="browser-default">
        <li>Separated - Mark user as separated in the database. The user record will continue to exist in the database and will show up in the separated list of users.</li>
    </ul>
    Note: If a user is destroyed, but still in LDAP, they will be re-created with no records on the next login.
</p>
