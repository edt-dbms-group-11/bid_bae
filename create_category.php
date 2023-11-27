<?php include_once("header.php") ?>
<?php include_once('database.php') ?>
<?php include_once("database_functions.php") ?>

<div class="container">
    <div style="max-width: 800px; margin: 10px auto">
        <h2 class="my-3">Create new category</h2>
        <div class="card">
            <div class="card-body">
                <form method="post" action="create_category_result.php">
                    <div class="form-group row">
                        <label for="categoryTitle" class="col-sm-2 col-form-label text-right">Category Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="categoryTitle" name="categoryTitle">
                            <small id="categoryname" class="form-text text-muted"><span class="text-danger">*
                                    Required.</span> A short description of the category being registered, which will be
                                displayed on the item page.</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="categoryDesc" class="col-sm-2 col-form-label text-right">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="categoryDesc" rows="4" name="categoryDesc"></textarea>
                            <small id="detailsHelp" class="form-text text-muted">Full details of the new
                                category</small>
                        </div>
                    </div>
            </div>
            <button type="submit" name="categorysubmit" class="btn btn-primary form-control">Create Category</button>
            </form>
        </div>
    </div>
</div>

</div>

<?php include_once("footer.php") ?>