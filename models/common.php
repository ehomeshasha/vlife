<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}
class common {
	
	function common($table) {
		$this->table=$table;
	}
	function getAll($container='',$field=array(),$order='',$limit='',$table='',$sql=''){
		$table = !empty($table) ? $table: $this->table;
		if(empty($sql))
		{
			if($field)
			{
				$field = "`".implode("`,`",$field)."`";
			}
			else
			{
				$field = '*';
			}
			$sql = "select ".$field." from ".tname($table)." where 1 ".$container." ".$order." ".$limit;
		}
		return $GLOBALS['db']->fetch_all($sql);
	}
	//取出一条数据
	function GetOne($container='',$field=array(),$order='',$table='',$sql='') {
		$table = !empty($table) ? $table: $this->table;
		if(empty($sql))
		{
			if($field)
			{
				$field = "`".implode("`,`",$field)."`";
			}
			else
			{
				$field = '*';
			}
			$sql = "select ".$field." from ".tname($table)." where 1 ".$container." ".$order;
		}
		//echo $sql;
		return $GLOBALS['db']->fetch_first($sql);
	}
	//数组分页
	function ArrayPage($pag=0,$data=array(),$pagesize=20,$url='')
	{
		$p=$pag*$pagesize;
		$a=array();
		for($i=$p;$i<$p+$pagesize;$i++)
		{
			if(!empty($data[$i]))
			{
				$a[]=$data[$i];
			}
		}
		$count = count($data);
		$total = ceil($count/$pagesize);
		$showpage = array('count'=>$count,'total'=>$total,'prevpage'=>($pag>1?$page-1:0),'nextpage'=>($pag<$total?$page+1:$total),'absolutepage'=>$pag,'url'=>$url);
		$pageinfo = '';
		if(!($pag ==0))
		{
			$pageinfo.="<li class='pagefirst prevpage'><A HREF='".$showpage['url']."&page=".$showpage['prevpage']."'>Previous</A></li>";
		}
		for($v=0;$v<$total;$v++)
		{
			if($v==$showpage['absolutepage'])
			{
				$pageinfo.="<li class='current'>".($v+1)."</li>";
			}
			else
			{
				$pageinfo.="<li class='current'><a href='".$showpage['url']."&page=".$v."'>".($v+1)."</a></li>";
			}
		}

		if(!($pag==$total-1))
		{
			$pageinfo.="<li class='pagefirst nextpage'><A HREF='".$showpage['url']."&page=".$showpage['nextpage']."'>Next</A></li>";
			$pageinfo.="<li class='pagefirst'><A HREF='".$showpage['url']."&page=".($showpage['total']-1)."'><img src='views/default/images/last.gif' border='0'></A></li>";
		}

		return array('data'=>$a,'pageinfo'=>$pageinfo);
	}

	//取出多条数据并带分页
	//$showpage=array('isshow'=>1,'currentpage'=>1,'pagesize'=>20,'url'=>'index.php','example'=>1);
	function GetPage($showpage,$container='',$limit='',$field=array(),$order='',$table='',$sql='',$sor)
	{
		if($showpage['group'] ) $group = " group  by {$showpage['group']} ";
		$table = !empty($table) ? $table : $this->table;
		if(empty($sql)) {
			if($field) {
				$field = "`".implode("`,`",$field)."`";
			} else {
				$field = '*';
			}
			$sql = "select distinct ".$field." from ".tname($table)." where 1=1 ".$container." ".$group.$order;
		}
		//echo $sql;
		if($showpage['isshow'] == 1)
		{
			
			if($sql=='')
				$count = $this->GetCount($container,$field,$table);
			else{
				$t_sql = " select count(*) as total from ({$sql}) a";
				$c = $GLOBALS['db']->fetch_first($t_sql);
				$count= $c['total'];
			}

			if($count==0)	{ /*echo "<br/>".$t_sql."<br/>";*/	return null;	}
			$page = new page($count);
			$page->pageSize = $showpage['pagesize'];
			$page->setPage();
			$page->url = $showpage['url'];
			$button = $page->setFormatPage();
			if($showpage['currentpage']>$page->total){
				$showpage['currentpage'] = $page->total - 1;
			}
			if($page->total >9 && $showpage['currentpage']>=8 && $showpage['currentpage']<$page->total){
				$s = $showpage['currentpage']-3;
				if($page->total - $showpage['currentpage'] >8)	##等待修改			
					$button = range($s,$s+9); 
				else if($page->total - $showpage['currentpage'] ==0){
					$button =NULL;
				}else
					$button = range($s,$page->total-1);
			}
			
			$page->parseVal();
			$pageinfo = '';
			
			if($showpage['example'] == 1)
			{
				if($count>$showpage['pagesize'])
				{
					if($showpage['currentpage']>0)
					{
						$pageinfo .= "<li class='pagefirst'><a href='javascript:fen(0)'><img src='views/default/images/home.gif' border='0'></a></li>";
						$pageinfo .= "<li class='pagefirst prevpage'><A HREF='javascript:fen(".$page->prevPage.")'>Previous</A></li>";
					}

					foreach($button as $key => $v)
					{
						if($v == $page->absolutePage)
						{
							$pageinfo .= "<li class='current'>".($v+1)."</li>";
						}
						else
						{
							$pageinfo .= "<li class='normal'><a href='javascript:fen(".$v.")'>".($v+1)."</a></li>";
						}
					}
					if($page->nextPage < $page->total)
					{
						$pageinfo .= "<li class='pagefirst nextpage'><A HREF='javascript:fen(".$page->nextPage.")'>Next</A></li>";
						$pageinfo .= "<li class='pagefirst lastpage'><A HREF='javascript:fen(".($page->total>1?$page->total-1:0).")'><img src='views/default/images/last.gif' border='0'></A></li>";
					}
				}
				else
				{
					//$pageinfo .= "<li class='current'><a href=''>1</a></li>";
				}

			}
			if($showpage['example'] == 2)
			{
				$pageinfo .= "<li class='pagefirst'><a href='".$page->url."=0'><img src='views/default/images/home.gif' border='0'></a></li>";
				if($page->prevPage > -1)
				{
					$pageinfo .= "<li class='pagefirst prevpage'><A HREF='".$page->url."=".$page->prevPage."'>Previous</A></li>";
				}
				foreach($button as $key => $v)
				{
					if($v == $_GET['page'])
					{
						$pageinfo .= "<li class='current'><a href='".$page->url."=".$v."'>".($v+1)."</a></li>";
					}
					else
					{
						$pageinfo .= "<li class='normal'><a href='".$page->url."=".$v."'>".($v+1)."</a></li>";
					}
				}
				if($page->nextPage < $page->total)
				{
					$pageinfo .= "<li class='pagefirst nextpage'><A HREF='".$page->url."=".$page->nextPage."'>Next</A></li>";
				}
				$pageinfo .= "<li class='pagefirst lastpage'><A HREF='".$page->url."=".($page->total>1?$page->total-1:0)."'><img src='views/default/images/last.gif' border='0'></A></li>";
			}
		}
		$rows = array();
		
		if($showpage['isshow'] == 1) {
			$limit = "limit ".abs(intval($showpage['currentpage'])*intval($showpage['pagesize'])).",".intval($showpage['pagesize']>0?$showpage['pagesize']:20);
		}
		
		$sql .= " ".$limit;

 //echo "<br/>".$sql."<br/>";
 //exit;
		$query = $GLOBALS['db']->query($sql);
		while($result = $GLOBALS['db']->fetch_array($query))
		{
			$rows[] = $result;
		}
		if($showpage['isshow']==1)
		{
			return array('page'=>(array)$page,'pageinfo'=>$pageinfo,'data'=>$rows,'sor'=>$sor);
		}
		else
		{
			return $rows;
		}
	}

	function GetLeftLimit($showpage,$fields='',$lefttable='',$field1='',$field2='',$container='',$limit='',$order='',$sor='',$table='',$sql='')
	{
		$righttable = !empty($table) ? $table : $this->table;
		if(empty($sql))
		{
			if(!empty($fields))
			{
				$array = explode(',',$fields);
				foreach ($array as $v){
					$field .= 'b.'.$v.',';
				}
			}
			else
			{
				$field='b.*,';
			}
			$sql = "select ".$field."a.* from ".tname($righttable)." as a left join ".tname($lefttable)." as b on a.$field1 = b.$field2 where 1 ".$container." ".$order;
			if($showpage['isshow']==1)
			{
				$limit = "limit ".abs(intval($showpage['currentpage'])*intval($showpage['pagesize'])).",".intval($showpage['pagesize']>0?$showpage['pagesize']:20);
			}
			$sql .= " ".$limit;
		}
		if($showpage['isshow']==1)
		{

			$count = $this->GetLeftCount($container,'',$righttable);
			$page = new page($count);
			$page->pageSize = $showpage['pagesize'];
			$page->setPage();
			$page->url = $showpage['url'];
			$button = $page->setFormatPage();
			$page->parseVal();
			$pageinfo = '';
			if($showpage['example'] == 1)
			{
				$pageinfo .= "<li class='pagefirst'><a href='javascript:fen(0)'><img src='views/default/images/home.gif' border='0'></a></li>";
				if($page->prevPage > -1)
				{
					$pageinfo .= "<li class='pagefirst prevpage'><A HREF='javascript:fen(".$page->prevPage.")'>Previous</A></li>";
				}

				foreach($button as $key => $v)
				{
					if($v == $page->absolutePage)
					{
						$pageinfo .= "<li class='current'><a href='javascript:fen(".$v.")'>".($v+1)."</a></li>";
					}
					else
					{
						$pageinfo .= "<li class='normal'><a href='javascript:fen(".$v.")'>".($v+1)."</a></li>";
					}
				}
				if($page->nextPage < $page->total)
				{
					$pageinfo .= "<li class='pagefirst nextpage'><A HREF='javascript:fen(".$page->nextPage.")'>Next</A></li>";
				}
				$pageinfo .= "<li class='pagefirst lastpage'><A HREF='javascript:fen(".($page->total>1?$page->total-1:0).")'><img src='views/default/images/last.gif' border='0'></A></li>";
			}
			if($showpage['example'] == 2)
			{
				$pageinfo .= "<li class='pagefirst'><a href='".$page->url."=0'><img src='views/default/images/home.gif' border='0'></a></li>";
				if($page->prevPage > -1)
				{
					$pageinfo .= "<li class='pagefirst prevpage'><A HREF='".$page->url."=".$page->prevPage."'>Previous</A></li>";
				}

				foreach($button as $key => $v)
				{
					if($v == $page->absolutePage)
					{
						$pageinfo .= "<li class='current'><a href='".$page->url."=".$v."'>".($v+1)."</a></li>";
					}
					else
					{
						$pageinfo .= "<li class='normal'><a href='".$page->url."=".$v."'>".($v+1)."</a></li>";
					}
				}
				if($page->nextPage < $page->total)
				{
					$pageinfo .= "<li class='pagefirst nextpage'><A HREF='".$page->url."=".$page->nextPage."'>Next</A></li>";
				}
				$pageinfo .= "<li class='pagefirst lastpage'><A HREF='".$page->url."=".($page->total>1?$page->total-1:0)."'><img src='views/default/images/last.gif' border='0'></A></li>";
			}
		}
		$rows = array();
		$query = $GLOBALS['db']->query($sql);
		while($result = $GLOBALS['db']->fetch_array($query))
		{
			$rows[] = $result;
		}
		if($showpage['isshow'] == 1)
		{
			return array('page'=>(array)$page,'pageinfo'=>$pageinfo,'data'=>$rows,'sor'=>$sor);
		}
		else
		{
			return $rows;
		}
	}
	//取出总数
	function GetLeftCount($container,$field='*',$table='')
	{
		$mytable = !empty($table) ? tname($table) : tname($this->table);
		$sql = "select count(".($field?$field:'*').") as total from ".$mytable." as a where 1 ".$container;
		$count = $GLOBALS['db']->fetch_first($sql);
		return intval($count['total']);
	}
	//取出总数
	function GetCount($container,$field='*',$table='')
	{
		$mytable = !empty($table) ? tname($table) : tname($this->table);
		$sql = "select count(*) as total from (select distinct ".($field?$field:'*')." from ".$mytable." where 1 ".$container.')a';
		$count = $GLOBALS['db']->fetch_first($sql);
		return intval($count['total']);
	}
	//取出总和
	function GetSum($container,$field,$table='')
	{
		$mytable = !empty($table) ? tname($table) : tname($this->table);
		$sql = "select sum(".$field.") as msum from ".$mytable." where 1 ".$container;

		$count = $GLOBALS['db']->fetch_first($sql);
		return intval($count['msum']);
	}
	//插入数据
	function insert($data,$table,$replace) {
		$table = !empty($table) ? $table : $this->table;
		$f=$v='';
		$i=0;
		foreach($data as $key=>$val) {
			$d=$i>0?',':'';
			$f.=$d.'`'.$key.'`';
			$v.=$d."'".global_addslashes(trim($val))."'";
			$i++;
		}
		$type=$replace?'replace':'insert';
		$sql = $type." into ".tname($table)."(".$f.") values(".$v.")";
		if($GLOBALS['db']->query($sql)) {
			return $GLOBALS['db']->insert_id();
		}
		return false;
	}
	
	//更新数据
	function UpdateData($data,$container,$table='',$quot = "'") {
		$table = !empty($table) ? $table : $this->table;
		$i = 0;
		$f = '';
		foreach($data as $key=>$val) {
			$d=$i>0?',':'';
			$f.=$d."`".$key."`="."$quot".global_addslashes(trim($val))."$quot";
			$i++;
		}
		$sql="update ".tname($table)." set ";
		$sql.=$f." where 1 ".$container;
		if($GLOBALS['db']->query($sql)) {
			return true;
		}
		return false;
	}

	//删除数据
	function DeleteData($container,$table='') {
		if(!empty($container)) {
			$mtable=!empty($table)?$table:$this->table;
			$sql="delete from ".tname($mtable)." where ".$container;
			if($GLOBALS['db']->query($sql)) {
				return true;
			}
		}
		return false;
	}
	function JoinData($str1,$str2,$gototabel,$container,$talbe='')
	{
		$mtable=!empty($talbe)?$talbe:$this->table;
		$sql = "INSERT INTO ".tname($gototabel)."($str1) SELECT $str2 FROM ".tname($mtable)." WHERE 1=1 $container";

		if($GLOBALS['db']->query($sql))
		{
			return true;
		}else{
			return false;
		}
	}
}
