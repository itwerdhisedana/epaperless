<!-- PAGE CONTENT BEGINS -->
<form class="form-horizontal" role="form" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
	<div class="row">
		<div class="col-sm-12">
			<div class="widget-box">
				<div class="widget-header">
					<h4 class="widget-title">Laporan Progress Surat Masuk</h4>
					<span class="widget-toolbar">
						<a href="#" data-action="collapse">
							<i class="ace-icon fa fa-chevron-up"></i>
						</a>
						<a href="#" data-action="close">
							<i class="ace-icon fa fa-times"></i>
						</a>
					</span>
				</div>
				<div class="widget-body">
					<div class="widget-main">
						<label for="id-date-range-picker-1">Filter Tanggal Surat</label><span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Checklist pada tombol yang tersedia untuk mengaktifkan filter ini. Tentukan range Tanggal yang akan di filter." title="Filter Tanggal">?</span>
						<div class="row">
							<div class="col-xs-8 col-sm-8">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-calendar bigger-110"></i>
									</span>
									<input class="form-control" type="text" name="rangetgl" id="id-date-range-picker-1" />
								</div>
							</div>
							<div class="col-xs-8 col-sm-1">
								<div class="input-group">
									<label>
										<input name="filterTgl" type="checkbox" class="ace" value="1"/>
										<span class="lbl"> </span>
									</label>
								</div>
							</div>
						</div>
						<div class="space-6"></div>
						<label>Filter No Agenda / Nomor surat</label>
						<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Checklist pada tombol yang tersedia untuk mengaktifkan filter ini. Tentukan No Agenda surat yang ingin di filter." title="Filter User">?</span>
						<div class="row">
							<div class="col-xs-8 col-sm-8">
								<div class="input-group">
									<input class="form-control" type="text" name="noagenda"/>
								</div>
							</div>
							<div class="col-xs-8 col-sm-1">
								<div class="input-group">
									<label>
										<input name="filterAgenda" type="checkbox" class="ace" value="1"/>
										<span class="lbl"> </span>
									</label>
								</div>
							</div>
						</div>						
						<div class="space-6"></div>
						<div class="row">
							<div class="col-xs-8 col-sm-2">
								<div class="input-group">
									<button type="submit" class="btn btn-info" type="button">
										<i class="ace-icon fa fa-check bigger-110"></i>
										Submit
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){?>
	<div class="row">
		<div class="col-sm-12">
			<div class="widget-box">
				<div class="widget-body">
					<div class="widget-main"><?php
						$wherekCon = '';
						if(isset($_POST['filterTgl'])){
							$filterTgl = htmlspecialchars($purifier->purify(trim($_POST['filterTgl'])), ENT_QUOTES);
							$from = explode("/", substr($_POST['rangetgl'],0,10));
							$tglfrom = htmlspecialchars($from[2]."-".$from[0]."-".$from[1]);
							$to = explode("/", substr($_POST['rangetgl'],-10));
							$tglto = htmlspecialchars($to[2]."-".$to[0]."-".$to[1]);
							$wherekCon .= "WHERE tgl_surat between '$tglfrom' AND '$tglto'";
						}else{
							$tglfrom = $tglto = '';
						}
						if(isset($_POST['filterAgenda'])){
							$noagenda = htmlspecialchars($purifier->purify(trim($_POST['noagenda'])), ENT_QUOTES);
							if(isset($_POST['filterTgl']) AND $_POST['filterTgl'] != ''){
								$wherekCon .= "AND custom_noagenda = '$noagenda' OR no_sm = '$noagenda'";
							}else{
								$wherekCon .= " WHERE custom_noagenda = '$noagenda' OR no_sm = '$noagenda'";
							}
						}
						//print_r($_POST);
						//echo $wherekCon;
						$ArsipSM = $this->model->selectprepare("arsip_sm", $field=null, $params=null, $where=null, "$wherekCon order by tgl_terima ASC");
						if($ArsipSM->rowCount() >= 1){
							while($dataArsipSM = $ArsipSM->fetch(PDO::FETCH_OBJ)){
								$dump_sm[]=$dataArsipSM;
							}?>
								<center>
								<h4>
									Laporan Progress Surat<?php
									if(isset($_POST['filterTgl'])){?><br/>
										<b><?php echo tgl_indo($tglfrom);?></b> s/d <b><?php echo tgl_indo($tglto);?></b><?php
									}?>
								</h4></center>
								<table id="simple-table" class="table  table-bordered table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>No Agenda</th>
											<th>No Surat</th>
											<th>Tgl Surat</th>
											<th width="200">Perihal</th>
											<th width="140">Sumber Surat</th>
											<th width="400">Progress</th>
										</tr>
									</thead>
									<tbody><?php
										$no=1;
										foreach($dump_sm as $key => $object){
											$CekSM = $this->model->selectprepare("arsip_sm", $field=null, $params=null, $where=null, "WHERE id_sm = '$object->id_sm'");
											if($CekSM->rowCount() >= 1){
												$tglsurat = explode("-", $object->tgl_surat);
												$tglsurat = $tglsurat[2]."/".$tglsurat[1]."/".$tglsurat[0];
												$tgltrm = explode("-", $object->tgl_terima);
												$tgltrm = $tgltrm[2]."/".$tgltrm[1]."/".$tgltrm[0];?>
												<tr>
													<td><?php echo $no;?></td>
													<td><?php echo $object->custom_noagenda;?></td>
													<td><?php echo $object->no_sm;?></td>
													<td><?php echo tgl_indo1($object->tgl_surat);?></td>
													<td><?php echo $object->perihal;?></td>
													<td><?php echo $object->pengirim;?></td>
													<td><?php
														$CekProgress = $this->model->selectprepare("status_surat", $field=null, $params=null, $where=null, "WHERE id_sm = '$object->id_sm' order by statsurat ASC");
														while($dataCekProgress = $CekProgress->fetch(PDO::FETCH_OBJ)){
															$CekUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$dataCekProgress->id_user'")->fetch(PDO::FETCH_OBJ);
															if($dataCekProgress->statsurat == 1){
																$statusSirat = "Sedang diproses";
															}elseif($dataCekProgress->statsurat == 2){
																$statusSirat = "Selesai";
															}elseif($dataCekProgress->statsurat == 0){
																$statusSirat = "Dibatalkan";
															}?>
															<ul><li><b><?php echo $CekUser->nama;?></b> status : <b><?php echo $statusSirat;?></b> <br/><?php echo $dataCekProgress->ket;?><br/><?php echo tgl_indo1($dataCekProgress->created);?>, <?php echo substr($dataCekProgress->created,-9,-3);?> WIB</ul></li><?php
														}?>
													</td>
												</tr><?php
											}
										$no++;
										}?>
										<tr>
											<td colspan=8>
												<span class="label label-xs label-primary label-white middle">
													<a href="./index.php?op=report_progress_print&start=<?php echo $tglfrom;?>&to=<?php echo $tglto;?>&noagenda=<?php echo $noagenda;?>&filtertgl=<?php echo $_POST['filterTgl'];?>" target="_blank"><b>Print</b> <i class="ace-icon fa fa-print align-top bigger-125 icon-on-right"></i></a>
												</span>
											</td>
										</tr>
									</tbody>
								</table><?php
						}else{?>
							<div class="alert alert-danger">
								<button type="button" class="close" data-dismiss="alert">
									<i class="ace-icon fa fa-times"></i>
								</button>
								<p>
									<strong><i class="ace-icon fa fa-check"></i>Perhatian!</strong>
									Data tidak ditemukan. Terimakasih.
								</p>
								<p>
									<a href="./index.php?op=report_disposisi"><button class="btn btn-minier btn-danger">Kembali</button></a>
								</p>
							</div><?php
						}?>
					</div>
				</div>
			</div>
		</div>
	</div><?php
}?>