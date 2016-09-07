<h3>Welcome to qmvc!</h3>

<p>This is a dynamic variable name: <?php echo $title; ?>.</p>
<hr>
<p>This page was generated with a layout, a view, and an element.</p>

<p>This content you see here is the view, titled <?php echo $this->request->path["view"]; ?>.view.php.</p>

<p>The layout file controls the outer HTML portion.</p>

<p id="element"></p>
<hr>

<p>This is a simple registration call with the auth module:</p>

<p><?php echo $admin; ?></p>

<p><b>Here's our admin user from a database find:</b></p>

<pre>
    <?php var_dump($admin_user); ?>
</pre>
<hr>
<p>Did the login attempt succeed? <b><?php echo ($loggedin ? "Yes" : "No"); ?></b></p>
<p>Our login creds array:</p>
<pre>
    <?php var_dump($login_attempt); ?>
</pre>
<hr>
<p>Here's an ajax form example: (try submitting before typing anything)</p>

<?php
    $this->form->create(array('id' => 'ajaxform'));
    $this->form->input(array(
        'type' => 'text',
        'placeholder' => "Secret Code",
        'name' => 'secretcode',
    ));
    $this->form->submit(array('value' => "Submit Code"));
    $this->form->end();
?>