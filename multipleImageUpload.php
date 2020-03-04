<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>



<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <div class="alert alert-success alert-dismissible fade in">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Success!</strong> <?php echo $this->session->flashdata('statusMsg'); ?>
  </div>

        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>Name: </label>
                <input type="text" name="imageName" class="form-control" />
            </div>
    <div class="form-group">
        <label>Images Only JPEG, JPG and PNG </label>
        <input class="form-control"  type="file" name="files[]" multiple/>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary form-control" name="fileSubmit" value="UPLOAD"/>
    </div>
</form>
    </div>
    <div class="col-md-4">


    </div>

</div>


<script>

</script>

<!-- file upload form -->
