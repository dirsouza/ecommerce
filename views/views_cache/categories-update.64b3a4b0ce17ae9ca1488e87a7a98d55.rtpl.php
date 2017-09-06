<?php if(!class_exists('Rain\Tpl')){exit;}?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Lista de Categorias
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="/admin"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li><a href="/admin/categories">Categorias</a></li>
                        <li class="active">Editar Categoria</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Editar Categoria</h3>
                                </div>
                                <!-- /.box-header -->
                                <!-- form start -->
                                <form role="form" action="/admin/categories/<?php echo htmlspecialchars( $category["idcategory"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" method="post">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="descategory">Categoria</label>
                                            <input type="text" class="form-control" id="descategory" name="descategory" autofocus placeholder="Digite a categoria" value="<?php echo htmlspecialchars( $category["descategory"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer modal-footer">
                                        <button type="submit" class="btn btn-primary">Atualizar</button>
                                        <button type="button" class="btn btn-default" onclick="javascript: location.href = '/admin/categories'">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->