<!-- PAGE CONTENT BEGINS -->
<form class="form-horizontal" role="form" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
	<div class="row">
		<div class="col-sm-12">
			<div class="widget-box">
				<div class="widget-header">
					<h4 class="widget-title">Laporan Disposisi Surat</h4>
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
						<label for="id-date-range-picker-1">Filter Tanggal Disposisi</label><span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Checklist pada tombol yang tersedia untuk mengaktifkan filter ini. Tentukan range Tanggal yang akan di filter." title="Filter Tanggal">?</span>
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
						<label>Filter User Tujuan Disposisi</label>
						<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Checklist pada tombol yang tersedia untuk mengaktifkan filter ini. Tentukan user tujuan disposisi yg ingin di filter." title="Filter User">?</span>
						<div class="row">
							<div class="col-xs-8 col-sm-8">
								<div class="input-group">
									<select id="form-field-select-3" name="user" data-placeholder="Pilih User...">
										<option value="">Pilih User...</option><?php 
										$user = $this->model->selectprepare("user", $field=null, $params=null, $where=null, $other=null);
										while($dataUser= $user->fetch(PDO::FETCH_OBJ)){
											$dumpUser[]=$dataUser;?>
											<option value="<?php echo $dataUser->id_user;?>"><?php echo $dataUser->nama;?></option><?php
										}?>
									</select>
								</div>
							</div>
							<div class="col-xs-8 col-sm-1">
								<div class="input-group">
									<label>
										<input name="filterUser" type="checkbox" class="ace" value="1"/>
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
							$wherekCon .= "WHERE a.tgl between '$tglfrom' AND '$tglto'";
						}else{
							$tglfrom = $tglto = '';
						}
						if(isset($_POST['filterUser'])){
							$user = htmlspecialchars($purifier->purify(trim($_POST['user'])), ENT_QUOTES);
							$TampilUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$user'")->fetch(PDO::FETCH_OBJ);
							$namaUser = $TampilUser->nama;
							if(isset($_POST['filterTgl'] )AND $_POST['filterTgl'] == ''){
								$wherekCon .= "WHERE a.disposisi LIKE '%\"$user\"%'";
							}else{
								$wherekCon .= " AND a.disposisi LIKE '%\"$user\"%'";
							}
						}else{
							$user = $namaUser = '';
						}
						//print_r($_POST);
						//echo $wherekCon;
						$field = array("a.id_user as iduser_dis","a.*");
						$memo = $this->model->selectprepare("memo a join user c on a.id_user=c.id_user", $field, $params=null, $where=null, "$wherekCon order by a.tgl ASC");
						if($memo->rowCount() >= 1){
							while($data_memo = $memo->fetch(PDO::FETCH_OBJ)){
								$dump_sm[]=$data_memo;
							}?>
								<center>
								<h4>
									Data Disposisi surat <?php if($namaUser != ''){?> ke : <b><?php echo $namaUser;?></b><?php }
									if(isset($_POST['filterTgl'])){?><br/>
										<b><?php echo tgl_indo($tglfrom);?></b> s/d <b><?php echo tgl_indo($tglto);?></b><?php
									}?>
								</h4></center>
								<table id="simple-table" class="table  table-bordered table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th width="100">Tgl Disposisi</th>
											<th width="100">Tujuan Disposisi</th>
											<th width="180">Isi Disposisi</th>
											<th width="130">Didisposisi oleh</th>
											<th>No Agenda</th>
											<th>No Surat</th>
											<th width="200">Perihal</th>
											<th width="140">Pengirim</th>
										</tr>
									</thead>
									<tbody><?php
										$no=1;
										foreach($dump_sm as $key => $object){
											$CekSM = $this->model->selectprepare("arsip_sm", $field=null, $params=null, $where=null, "WHERE id_sm = '$object->id_sm'");
											if($CekSM->rowCount() >= 1){
												$DataCekSM = $CekSM->fetch(PDO::FETCH_OBJ);
												$CekUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$object->iduser_dis'");
												$DataUser = $CekUser->fetch(PDO::FETCH_OBJ);
												$tglsurat = explode("-", $object->tgl_surat);
												$tglsurat = $tglsurat[2]."/".$tglsurat[1]."/".$tglsurat[0];
												$tgltrm = explode("-", $object->tgl_terima);
												$tgltrm = $tgltrm[2]."/".$tgltrm[1]."/".$tgltrm[0];
												$tgl_memo = substr($object->tgl,0,10);
												$tgl_memo = explode("-", $tgl_memo);
												$tgl_memo = $tgl_memo[2]."/".$tgl_memo[1]."/".$tgl_memo[0];
												$targetDis = json_decode($object->disposisi, true);?>
												<tr>
													<td><center><?php echo $no;?></center></td>
													<td><?php echo $tgl_memo;?></td>
													<td><?php
														foreach($targetDis as $field => $value){
															$CekUserDis = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$value'")->fetch(PDO::FETCH_OBJ);
															echo "<ul><li>".$CekUserDis->nama ."</ul></li>";
														}?>
													</td>
													<td><?php echo $DataUser->nama;?></td>
													<td><?php echo $object->note;?></td>
													<td><?php echo $DataCekSM->custom_noagenda;?></td>
													<td><?php echo $DataCekSM->no_sm;?></td>
													<td><?php echo $DataCekSM->perihal;?></td>
													<td><?php echo $DataCekSM->pengirim;?></td>
												</tr><?php
											}
										$no++;
										}?>
										<tr>
											<td colspan=8>
												<span class="label label-xs label-primary label-white middle">
													<a href="./index.php?op=report_dispo_print&start=<?php echo $tglfrom;?>&to=<?php echo $tglto;?>&user=<?php echo $user;?>&filtertgl=<?php echo $_POST['filterTgl'];?>" target="_blank"><b>Print</b> <i class="ace-icon fa fa-print align-top bigger-125 icon-on-right"></i></a>
												</span>
												<span class="label label-xs label-danger label-white middle">
												<a href="./index.php?op=report_dispo_print&start=<?php echo $tglfrom;?>&to=<?php echo $tglto;?>&user=<?php echo $user;?>&filtertgl=<?php echo $_POST['filterTgl'];?>&act=pdf" target="_blank"><b>Pdf</b> <i class="ace-icon fa fa-file-pdf-o align-top bigger-125 icon-on-right"></i></a>
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