<?php
use_helper('Number');
?>
<html>
    <head>
        <title>test</title>
        <link rel="stylesheet" type="text/css" media="screen" href="/poscms/web/css/../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/../bootstrap/css/bootstrap-theme.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/../font-awesome/css/font-awesome.css" />
    </head>
    <body>
        <div class="container bs-docs-container">
            <form action="addItemsSubmit" method="post" enctype="multipart/form-data">
                <?php if ($sf_user->hasFlash('file_error')): ?>
                    <div class="alert alert-danger" style="display: block;">
                        <?php echo $sf_user->getFlash('file_error') ?>
                    </div>
                <?php endif; ?>
                <?php if ($sf_user->hasFlash('file_done')): ?>
                    <div class="alert alert-success" style="display: block;">
                        <?php echo $sf_user->getFlash('file_done') ?>
                    </div>
                <?php endif; ?>
                <table  class="table table-striped">
                    <tr>
                        <td colspan="2"><h1>Add Items</h1></td>

                    </tr>

                    <tr>
                        <td>Definition file(xml)</td>
                        <td> <input   type="file" name="defile"></td>
                    </tr>
                    <tr>
                        <td>Data file(csv)</td>
                        <td> <input   type="file" name="datafile"></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td> <input   type="submit" name="Submit" value="Submit"  class="btn btn-default"></td>
                    </tr>




                </table>
            </form>
        </div>
    </body>
</html>
