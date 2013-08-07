<?php if ($request->get('invalid')): ?>
<div class="alert alert-error">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	Invalid
</div>
<?php endif ?>
<form method="POST" action="<?= $uri->moduleUrl ?>process/register" enctype="multipart/form-data" class="form-horizontal">
	<div class="control-group">
		<label class="control-label" for="inputEmail">Email</label>
		<div class="controls">
			<input type="text" name="email" id="inputEmail" placeholder="Email">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="inputPassword">Name</label>
		<div class="controls">
			<input type="text" name="name" id="inputPassword" placeholder="Name">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="inputPassword">File</label>
		<div class="controls">
			<input type="file" name="photo"><br>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" value="submit" name="submit" id="submitBtn" class="btn">Submit</button>
		</div>
	</div>
</form>