
<h1>Add a Post</h1>

<?php echo notifications(); ?>

<section class="content">

	<form method="post" action="<?php echo current_url(); ?>" novalidate>
		<fieldset>
			<p>
    			<label for="title">Title:</label>
    			<input id="title" name="title" value="<?php echo Input::post('title'); ?>">
    			
    			<em>Your post&rsquo;s title.</em>
    		</p>
			
			<p>
			    <label for="slug">Slug:</label>
			    <input id="slug" autocomplete="off" name="slug" value="<?php echo Input::post('slug'); ?>">
			    
			    <em>The slug for your post (<code><?php echo $_SERVER['HTTP_HOST']; ?>/posts/<span id="output">slug</span></code>).</em>
			</p>
			
            <p>
                <label for="description">Description:</label>
                <textarea id="description" name="description"><?php echo Input::post('description'); ?></textarea>
                
                <em>A brief outline of what your post is about. Used in the post introduction, RSS feed, and <code>&lt;meta name="description" /&gt;</code>.</em>
            </p>
            
			<p>
			    <label for="html">Content:</label>
			    <textarea id="html" name="html"><?php echo Input::post('html'); ?></textarea>
			    
			    <em>Your post's main content. Enjoys a healthy dose of valid HTML.</em>
			</p>
			
			<p>
			    <label>Status:</label>
    			<select id="status" name="status">
    				<?php foreach(array('draft', 'archived', 'published') as $status): ?>
    				<option value="<?php echo $status; ?>" <?php if(Input::post('status') == $status) echo 'selected'; ?>>
    					<?php echo ucwords($status); ?>
    				</option>
    				<?php endforeach; ?>
    			</select>
    			
    			<em>Statuses: live (published), pending (draft), or hidden (archived).</em>
			</p>
		</fieldset>
		
		<fieldset>
		    <legend>Customise</legend>
		    <em>Here, you can customise your posts. This section is optional.</em>
		    
		    <p>
		        <label for="css">Custom CSS:</label>
		        <textarea id="css" name="css"><?php echo Input::post('css'); ?></textarea>
		        
		        <em>Custom CSS. Will be wrapped in a <code>&lt;style&gt;</code> block.</em>
		    </p>

            <p>
                <label for="js">Custom JS:</label>
                <textarea id="js" name="js"><?php echo Input::post('js'); ?></textarea>
                
                <em>Custom Javascript. Will be wrapped in a <code>&lt;script&gt;</code> block.</em>
            </p>
		</fieldset>
		
		<fieldset>
		    <legend>Custom fields</legend>
		    <em>Create custom fields here.</em>

			<div id="fields">
				<!-- Re-Populate post data -->
				<?php foreach(Input::post('field', array()) as $data => $value): ?>
				<?php list($key, $label) = explode(':', $data); ?>
				<p>
					<label><?php echo $label; ?></label>
					<input name="field[<?php echo $key; ?>:<?php echo $label; ?>]" value="<?php echo $value; ?>">
				</p>
				<?php endforeach; ?>
			</div>
		</fieldset>
			
		<p class="buttons">
			<button type="submit">Create</button>
			<button id="create" type="button">Create a custom field</button>
			<a href="<?php echo base_url('admin/posts'); ?>">Return to posts</a>
		</p>
	</form>

</section>

<script src="//ajax.googleapis.com/ajax/libs/mootools/1.4.1/mootools-yui-compressed.js"></script>
<script>window.MooTools || document.write('<script src="<?php echo theme_url('js/mootools.js'); ?>"><\/script>');</script>

<script src="<?php echo theme_url('js/helpers.js'); ?>"></script>
<script src="<?php echo theme_url('js/popup.js'); ?>"></script>
<script src="<?php echo theme_url('js/custom_fields.js'); ?>"></script>

<script>
	(function() {
		var slug = $('slug'), output = $('output');

		// call the function to init the input text
		formatSlug(slug, output);

		// bind to input
		slug.addEvent('keyup', function() {formatSlug(slug, output)});
	}());
</script>

