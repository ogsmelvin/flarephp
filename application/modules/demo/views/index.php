<form method="POST" action="<?= $uri->getModuleUrl() ?>index/receive" enctype="multipart/form-data" class="form-horizontal">
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
            <input type="file" name="file"><br>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn">Submit</button>
        </div>
    </div>
</form>