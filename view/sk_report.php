<!-- PAGE CONTENT BEGINS -->
<form class="form-horizontal" role="form" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
	<div class="row">
		<div class="col-sm-12">
			<div class="widget-box">
				<div class="widget-header">
					<h4 class="widget-title">Laporan Surat Keluar</h4>
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
						<label for="id-date-range-picker-1">Filter berdasarkan Range Tanggal</label>
						<div class="row">
							<div class="col-xs-8 col-sm-11">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-calendar bigger-110"></i>
									</span>
									<input class="form-control" type="text" name="rangetgl" id="id-date-range-picker-1" />
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
						//print_r($_POST);
						$from = explode("/", substr($_POST['rangetgl'],0,10));
						$tglfrom = htmlspecialchars($from[2]."-".$from[0]."-".$from[1]);
						$to = explode("/", substr($_POST['rangetgl'],-10));
						$tglto = htmlspecialchars($to[2]."-".$to[0]."-".$to[1]); 
						//echo "$tglfrom $tglto";
						$arsip_sm = $this->model->selectprepare("arsip_sk", $field=null, $params=null, $where=null, "where tgl_surat between '$tglfrom' and '$tglto' order by tgl_surat ASC");
						if($arsip_sm->rowCount() >= 1){
							while($data_sm = $arsip_sm->fetch(PDO::FETCH_OBJ)){
								$dump_sm[]=$data_sm;
							}?>
								<center><h4>Data Surat Keluar <b><?php echo tgl_indo($tglfrom);?></b> s/d <b><?php echo tgl_indo($tglto);?></b></h4></center>
								<table id="simple-table" class="table  table-bordered table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th width="100">No Agenda</th>
											<th>No Surat</th>
											<th width="150">Tgl Surat</th>
											<th width="140">Pengolah</th>
											<th width="140">Klasifikasi</th>
											<th width="200">Tujuan Surat</th>
											<th width="200">Perihal</th>
										</tr>
									</thead>
									<tbody><?php
										$no=1;
										foreach($dump_sm as $key => $object){
											$tglsurat = explode("-", $object->tgl_surat);
											$tglsurat = $tglsurat[2]."/".$tglsurat[1]."/".$tglsurat[0];					
											$CekKlasifikasi = $this->model->selectprepare("klasifikasi_sk", $field=null, $params=null, $where=null, "WHERE id_klas='$object->klasifikasi'");
											$ViewKlasifikasi = $CekKlasifikasi->fetch(PDO::FETCH_OBJ);?>
											<tr>
												<td><center><?php echo $no;?></center></td>
												<td><?php echo $object->custom_noagenda;?></td>
												<td><?php echo $object->no_sk;?></td>
												<td><?php echo $tglsurat;?></td>
												<td><?php echo $object->pengolah;?></td>
												<td><?php echo $ViewKlasifikasi->nama;?></td>
												<td><?php echo $object->tujuan_surat;?></td>
												<td><?php echo $object->perihal;?></td>
											</tr><?php
										$no++;
										}?>
										<tr>
											<td colspan=7>
												<span class="label label-xs label-primary label-white middle">
													<a href="./index.php?op=report_sk_print&start=<?php echo $tglfrom;?>&to=<?php echo $tglto;?>" target="_blank"><b>Print</b> <i class="ace-icon fa fa-print align-top bigger-125 icon-on-right"></i></a>
												</span>
												<span class="label label-xs label-danger label-white middle">
												<a href="./index.php?op=report_sk_print&start=<?php echo $tglfrom;?>&to=<?php echo $tglto;?>&act=pdf" target="_blank"><b>Pdf</b> <i class="ace-icon fa fa-file-pdf-o align-top bigger-125 icon-on-right"></i></a>
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
									<a href="./index.php?op=report_sk"><button class="btn btn-minier btn-danger">Kembali</button></a>
								</p>
							</div><?php
						}?>
					</div>
				</div>
			</div>
		</div>
	</div><?php
}?>