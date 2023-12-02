<?php include_once("header.php")?>
<?php include_once('database.php') ?>
<?php include_once("database_functions.php")?>

<div class="container">
<div style="max-width: 800px; margin: 10px auto">
  <h2 class="my-3">Create new item</h2>
  <div class="card">
    <div class="card-body">
      <form method="post" action="create_item_result.php" enctype="multipart/form-data">>
        <div class="form-group row">
          <label for="itemTitle" class="col-sm-2 col-form-label text-right">Item Name</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="itemTitle" placeholder="e.g. Vase" name="itemTitle">
            <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> A short description of the item being registered, which will be displayed on the item page.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="itemDesc" class="col-sm-2 col-form-label text-right">Description</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="itemDesc" rows="4" name="itemDesc"></textarea>
            <small id="detailsHelp" class="form-text text-muted">Full details of the item to help bidders understand the product.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionCategory" class="col-sm-2 col-form-label text-right" >Category</label>
          <div class="col-sm-10">
            <!-- Load categories dynamically from the database -->
            <?php
            $categories = getCategoriesFromDatabase(); // Implement a function to fetch categories
            ?>
            <select class="form-control" id="auctionCategory" name="auctionCategory">
              <option selected>Choose...</option>
              <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['name'];?></option>
              <?php endforeach;?>
            </select>
            <small id="categoryHelp" class="form-text text-muted"><span class="text-danger" >* Required.</span> Select a category for this item.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="fileUpload" class="col-sm-2 col-form-label text-right">Select an image to upload</label>
          <div class="col-sm-10">
              <input type="file" class="form-control" id="fileUpload" name="fileUpload" accept =".jpg, .jpeg, .png">
            <small id="image" class="form-text text-muted"><span class="text-danger">* Required.</span> View of the item.</small>
          </div>
        </div>
        <button type="submit" name = "itemsubmit" class="btn btn-primary form-control">Create Item</button>
      </form>
    </div>
  </div>
</div>

</div>

<?php include_once("footer.php")?>