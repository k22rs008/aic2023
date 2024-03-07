<?php
namespace aic;
use aic\models\Instrument;
use aic\models\KsuCode;
use aic\views\Html;

$inst_id = $status = 0;
$selected_y = date('Y');
$selected_m = date('m');
$selected_d = 0;
$selected_t = 7;

if (isset($_POST['id'], $_POST['status'], $_POST['y'], $_POST['m'])){
    $selected_d = isset($_POST['d']) ? $_POST['d'] : $selected_d;
    $selected_t = isset($_POST['t']) ? $_POST['t'] : $selected_t;
    $_SESSION['selected_inst'] = $_POST['id'];
    $_SESSION['selected_status'] = $status = $_POST['status'];
    $_SESSION['selected_year'] = $selected_y = $_POST['y'];
    $_SESSION['selected_month'] = $selected_m = $_POST['m'];
    $_SESSION['selected_day'] = $selected_d;
    $_SESSION['selected_timespan'] = $selected_t; //period
}else if(isset($_SESSION['selected_inst'],$_SESSION['selected_status'])){
    $inst_id = $_SESSION['selected_inst'];
    $status = $_SESSION['selected_status'];
    $selected_y = $_SESSION['selected_year'];
    $selected_m = $_SESSION['selected_month'];
    $selected_d = $_SESSION['selected_day'];
    $selected_t = $_SESSION['selected_timespan'];
}
$date1 = $date2 = null;
if ($selected_y > 0 and $selected_m > 0){
    $day = $selected_d > 0 ? $selected_d : 1;
    $date = new \DateTimeImmutable($selected_y .'-'.$selected_m.'-'.$day);
    $def = [1=>'P1D', 7=>'P1W', 30=>'P1M',];
    $period = new \DateInterval($def[$selected_t]); 
    $date1 = $date->format('Y-m-d 00:00'); 
    $date2 = $date->add($period)->format('Y-m-d 00:00');
    // echo $date1, ', ', $date2;
}
?>
<script>
  $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<?php
echo '<div class="text-left">' . PHP_EOL;
echo '<form method="post" action="?do=rsv_list" class="form-inline">'. PHP_EOL;
echo '<div class="form-group mb-2">'. PHP_EOL;
$rows = (new Instrument)->getList();
$options = Html::toOptions($rows, 'id', 'shortname', [0=>'～全ての機器～']);
echo Html::select($options, 'id', [$inst_id]);
$options = Html::rangeOptions(date('Y')-1, date('Y')+1, '年');
echo Html::select($options, 'y', [$selected_y]);
$options = Html::rangeOptions(1, 12, '月');
echo Html::select($options, 'm', [$selected_m]);
$options = Html::rangeOptions(1, 31, '日', [0=>'日選択']);
echo Html::select($options, 'd', [$selected_d]);
$options = [1=>'１日間', 7=>'１週間', 30=>'１ヶ月',];
echo Html::select($options, 't', [$selected_t]);
$rsv_status = KsuCode::RSV_STATUS;
$rsv_status[0] = '全て';
ksort($rsv_status);
echo Html::select($rsv_status, 'status', [$status]);
echo '<button type="submit" class="btn btn-outline-primary m-1" data-placement="top" data-toggle="tooltip" title="条件で絞り込む">絞込</button>' . PHP_EOL; 
echo '<a class="btn btn-outline-success m-1" href="?do=rsv_report" data-placement="top" data-toggle="tooltip" title="利用者数集計">集計</a>' . PHP_EOL;
echo '<a class="btn btn-outline-success m-1" href="?do=rsv_excel"  data-placement="top" data-toggle="tooltip" title="利用状況Excel出力">出力</a>' . PHP_EOL;
echo '</div>'. PHP_EOL;
echo '</form>'. PHP_EOL;
echo '</div>' . PHP_EOL;