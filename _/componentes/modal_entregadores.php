<!--Inicio Modal status.-->
<div class="modal fade" id="modal-entregadores" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="myModalLabel">Atribuir um entregador</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="status">Selecione um entregador</label>
                            <select class="form-control" id="entregador" name="entregador">
                                <option value="0">Desatribuir</option>
                                <?php
                                $motoristas = $m -> get_motoristas($cidade_id, true);
                                foreach ($motoristas as $motorista) { ?>
                                    <option value="<?php echo $motorista['id']; ?>"><?php echo $motorista['nome']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="btn-alterar-entregador">Salvar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Fim Modal status.-->