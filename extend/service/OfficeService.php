<?php
/**
 * Created by bobo.
 * DateTime: 2019/8/16 15:55
 * Function:Office服务组件
 */
namespace service;

use think\Db;
use think\Model;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class OfficeService extends Model{

    //表行名
    private static $cellKey = array(
        'A','B','C','D','E','F','G','H','I','J','K','L','M',
        'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
        'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
    );

    //设置style
    private static $styleArray = [
        'allborders'=>[
//                PHPExcel_Style_Border里面有很多属性，想要其他的自己去看
//                'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,//边框是粗的
//                'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,//双重的
//                'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,//虚线
//                'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,//实粗线
//                'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHDOT,//虚粗线
//                'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHDOTDOT,//点虚粗线
            'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //细边框
            //'color' => array('argb' => 'FFFF0000'),
        ],
        'font' => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'=>\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
        ],
    ];

    /*
     * 读取Excel
     * @param $fp string 文件路径
     * @param $keys string|array 键名
     * @return array
     */
    public static function getArray($fp,$key=''){
        $reader = IOFactory::createReaderForFile($fp);
        $fData = $reader->load($fp)->getActiveSheet()->toArray();
        $keys = is_string($key) ? explode(',',$key) : $key;
        if(empty($keys))  return $fData;
        $data = array_map(function($item) use ($keys){
            return array_combine($keys,$item);
        },$fData);
        return $data;
    }

    /*
     * 导入sql
     * @param $fp string 文件路径
     * @param $fd string|array 字段名
     * @param $tn string 数据表名
     * @param $sl int 开始行数
     * @param $el int 结束行数
     * @return boole
     */
    public static function toSql($fp,$tn,$fd='',$sl=1,$el=null){
        $fdata = self::toArray($fp,$fd);
        $data = array_slice($fdata,$sl-1,$el,true);
        return self::updateAll($tn,$data);
    }


    /*
     * 导出Excel
     * @param $table string 数据库表
     * @param $condition string|array 查询条件
     * @param $fname string 保存文件名
     * @param $title string 表头
     * @param $key string|array 数据库字段
     * @return file
     */
    public function toExcel($table,$condition,$fname,$title='',$key=''){
        $keys = is_string($key)?explode(",",$key):$key;
        $fields = empty($keys) ? "*":$keys;
        //$key需要与数据库字段保持一致，否则会出错，文件保存后可以手工修改字段对应意思方便阅读
        $data = Db::name($table)->where($condition)->field($fields)->select();
        if(empty($data)) $this->error('查询数据为空','');
        $spreadSheet = new Spreadsheet();
        $begin = 1;
        $fname = empty($fname) ? date("Ymd_His").".xlsx" : $fname.".xlsx";
        $col_num = count($data[0]);
        if(!empty($title)){
            $spreadSheet->getProperties()->setTitle($title);
            $spreadSheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadSheet->getActiveSheet()->mergeCells('A1:'.self::$cellKey[$col_num-1].'1');
            $begin +=1;
        }
        if(!empty($keys)){
            for($i=0;$i<$col_num;$i++){
                $spreadSheet->getActiveSheet()->getStyle(self::$cellKey[$i].'2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            array_unshift($data,$keys);
        }
        $leng = sizeof($data);
        for($row = 0;$row < $leng;$row++){
            $j = $row+$begin;
            for($t=0;$t<$col_num;$t++){
                $spreadSheet->getActiveSheet()->setCellValueByColumnAndRow($t+1,$j,$data[$row][$keys[$t]]);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fname . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadSheet, 'Xlsx');
        $writer->save('php://output');

    }




















    /*
     *批量更新数据
     */
    protected function updateAll($table,$list){
        return $this->name($table)->saveAll($list);
    }



}