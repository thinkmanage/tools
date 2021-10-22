<?php
namespace thinkmanage\tools;

//文件操作类
class File{
	
	/**
	 * 创建(文件或目录)
	 *
	 * @param string $path 路径
	 * @param string $content 内容
	 * @param int $mode 访问权限
	 * @return boolean
	 */
	public static function create(string $path='', $content=null,int $mode=0777)
	{
		if(!$path){
			return false;
		}
		$path = self::pathReplace($path);
		//判断内容是否存在 如果内容不存在则创建目录
		if($content == null){
			return self::createDir($path, $mode);
		}else{
			return self::createFile($path, $mode, $content);
		}
	}
	
	/**
	 * 拷贝/移动(文件或目录)
	 *
	 * @param string $oldPath 原目录
	 * @param string $newPath 新目录
	 * @param string $type 类型(copy 拷贝 move 移动)
	 * @param boolean $overWrite 是否覆盖
	 * @return boolean
	 */
	public static function handle(string $oldPath='',string $newPath=null,string $type='copy',bool $overWrite=false){
		if(!$oldPath || !$newPath || !file_exists($oldPath)){
			return false;
		}
		$oldPath = self::pathReplace($oldPath);
		$newPath = self::pathReplace($newPath);
		//判断内容是否存在 如果内容不存在则创建目录
		if(is_dir($oldPath)){
			return self::handleDir($oldPath, $newPath, $type, $overWrite);
		}else{
			return self::handleFile($oldPath, $newPath, $type, $overWrite);
		}
	}
	
	/**
	 * 删除目录
	 *
	 * @param string $path 目录
	 * @return boolean
	 */
	public static function del(string $path=''){
		$oldPath = self::pathReplace($path);
		//目录为空或不存在
		if(!$path || !file_exists($path)){
			return false;
		}
		if (is_dir($path)){
			return self::delDir($path);
		}else{
			return self::delFile($path);
		}
	}
	
	
	/**
	 * 返回指定文件和目录的信息
	 * @param string $file
	 * @return ArrayObject
	 */
	public static function info(string $file){
		$file = self::pathReplace($file);
		$info = [];
		$info['filename']	= basename($file);//返回路径中的文件名部分。
		$info['pathname']	= realpath($file);//返回绝对路径名。
		$info['owner']		= fileowner($file);//文件的 user ID （所有者）。
		$info['perms']		= fileperms($file);//返回文件的 inode 编号。
		$info['inode']		= fileinode($file);//返回文件的 inode 编号。
		$info['group']		= filegroup($file);//返回文件的组 ID。
		$info['path']		= dirname($file);//返回路径中的目录名称部分。
		$info['atime']		= fileatime($file);//返回文件的上次访问时间。
		$info['ctime']		= filectime($file);//返回文件的上次改变时间。
		$info['perms']		= fileperms($file);//返回文件的权限。 
		$info['size']		= filesize($file);//返回文件大小。
		$info['type']		= filetype($file);//返回文件类型。
		$info['ext']		= is_file($file) ? pathinfo($file,PATHINFO_EXTENSION) : '';//返回文件后缀名
		$info['mtime']		= filemtime($file);//返回文件的上次修改时间。
		$info['isDir']		= is_dir($file);//判断指定的文件名是否是一个目录。
		$info['isFile']		= is_file($file);//判断指定文件是否为常规的文件。
		$info['isLink']		= is_link($file);//判断指定的文件是否是连接。
		$info['isReadable']	= is_readable($file);//判断文件是否可读。
		$info['isWritable']	= is_writable($file);//判断文件是否可写。
		$info['isUpload']	= is_uploaded_file($file);//判断文件是否是通过 HTTP POST 上传的。
		return $info;
	}
	
	/**
	 * 创建多级目录
	 * 
	 * @param string $path 目录路径
	 * @param int $mode 权限
	 * @return boolean
	 */
	public static function createDir(string $path='',int $mode=0777){
		$path = self::pathReplace($path);
		//路径为空
		if(!$path || is_file($path)){
			return false;
		}
		//存在目录则不创建
		if(is_dir($path)){
			return true;
		}
		return mkdir($path, true, $mode);
	}
	
	/**
	 * 拷贝/移动目录
	 *
	 * @param string $oldPath 原目录
	 * @param string $newPath 新目录
	 * @param string $type 类型(copy 拷贝 move 移动)
	 * @param boolean $overWrite 是否覆盖
	 * @return boolean
	 */
	public static function handleDir(string $oldPath='',string $newPath='',string $type='copy',bool $overWrite=false){
		$oldPath = self::pathReplace($oldPath);
		$newPath = self::pathReplace($newPath);
		//原目录或新目录不存在或源目录不是文件夹
		if(!$oldPath || !$newPath || !is_dir($oldPath)){
			return false;
		}
		//创建新目录
		if (!is_dir($newPath)){
			self::createDir($newPath);
		}
		$dir = opendir($oldPath);
		if (!$dir){
			return false;
		}
		$bool = true;
		while (false !== ($file = @readdir($dir))){
			if ($file=='.' || $file=='..'){
				 continue;
			}
			if (is_dir($oldPath.DIRECTORY_SEPARATOR.$file)){
				self::handleDir($oldPath.DIRECTORY_SEPARATOR.$file, $newPath.DIRECTORY_SEPARATOR.$file,$type,$overWrite);
			}else{
				$bool = self::handleFile($oldPath.DIRECTORY_SEPARATOR.$file, $newPath.DIRECTORY_SEPARATOR.$file,$type,$overWrite);
			}
		}
		closedir($dir);
		switch ($type){
			case 'copy':
				return $bool;
				break;
			case 'move':
				return rmdir($oldPath);
				break;
			default:
				throw new \Exception('类型错误');
		}
	}
	
	
	/**
	 * 删除目录
	 *
	 * @param string $path 目录
	 * @return boolean
	 */
	public static function delDir(string $path=''){
		$path = self::pathReplace($path);
		//目录为空或不存在
		if(!$path || !is_dir($path)){
			return false;
		}
		$dir = @opendir($path);
		while (false !== ($file = @readdir($dir))){
			if (($file != '.') && ($file != '..')){
				if (is_dir($path.DIRECTORY_SEPARATOR.$file)){
					self::delDir($path.DIRECTORY_SEPARATOR.$file);
				}else{
					self::delFile($path.DIRECTORY_SEPARATOR.$file);
				}
			}
		}
		closedir($dir);
		//删除当前文件夹
		return rmdir($path);
	}
	
	/**
	 * 判断目录是否为空
	 * 
	 * @param string $dir
	 * @return boolean
	 */
	public static function dirIsEmpty(string $dir){
		$dir = self::pathReplace($dir);
		//目录为空或不存在
		if(!$dir || !is_dir($dir)){
			throw new \Exception('目录不存在或为空');
		}
		$handle = opendir($dir);
		while (($file = readdir($handle)) !== false){
			if ($file != '.' && $file != '..'){
				closedir($handle);
				return true;
			}
		}
		closedir($handle);
		return false;
	}
	
	/**
	 * 目录下指定编码转换
	 *
	 * @param string $dir 目录路径
	 * @param string $oldCode 原始编码
	 * @param string $newCode 输出编码
	 * @param array $exts 文件类型
	 * @param boolean $isChild 是否遍历子目录
	 * @return boolean
	 */
	public static function dirChangeCode(string $dir,string $oldCode,string $newCode,array $exts=['*'],bool $isChild=true){
		$dir = self::pathReplace($dir);
		if(!$oldCode || !$newCode){
			throw new \Exception('编码类型错误');
		}
		if(!is_dir($dir)){
			throw new \Exception('目录参数错误');
		}
		$fh = opendir($dir);
		while (($file = readdir($fh)) !== false){
			if (strcmp($file, '.')==0 || strcmp($file, '..')==0){
				continue;
			}
			$path = $dir.DIRECTORY_SEPARATOR.$file;
			if (is_dir($path) && $isChild==true){
				$files = self::dirChangeCode($path,$oldCode,$newCode,$exts,$isChild);
			}else{
				if(is_file($path) && (in_array('*',$exts) || in_array(self::getExtName($path),$exts))){
					$boole = self::fileChangeCode($path,$oldCode,$newCode);
					if(!$boole) continue;
				}
			}
		}
		closedir($fh);
		return true;
	}

	/**
	 * 获取指定目录下的信息
	 * @param string $dir 路径
	 * @return Array
	 */
	public static function getDirSize(string $dir){
		$dir = self::pathReplace($dir);
		if(!is_dir($dir)){
			throw new \Exception('目录类型错误');
		}
		$handle = @opendir($dir);//打开指定目录
		$directory_count = 0;
		$total_size = 0;
		$file_cout = 0;
		while (false !== ($file_path = readdir($handle))){
			if($file_path != "." && $file_path != ".."){
				$next_path = $dir.DIRECTORY_SEPARATOR.$file_path;
				if (is_dir($next_path)){
					$directory_count++;
					$result_value = self::getDirSize($next_path);
					$total_size += $result_value['size'];
					$file_cout += $result_value['file_count'];
					$directory_count += $result_value['dir_count'];
				}elseif (is_file($next_path)){
					$total_size += filesize($next_path);
					$file_cout++;
				}
			}	
		}
		closedir($handle);//关闭指定目录
		$result_value['size'] = $total_size;
		$result_value['count'] = $file_cout+$directory_count;
		$result_value['file_count'] = $file_cout;
		$result_value['dir_count'] = $directory_count;
		return $result_value;
	}
	
	/**
	 * 列出指定目录下符合条件的文件和文件夹
	 * @param string $dir 路径
	 * @param array $exts 需要列出的后缀名文件
	 * @param string $sort 数组排序
	 * @param boolean $isChild 是否遍历子目录
	 * @return array
	 */
	public static function getDirFileList(string $dir,array $exts=['*'],string $sort='asc',bool $isChild=true){
		$dir = self::pathReplace($dir);
		$sort = strtolower($sort);
		$files = [];
		if(!is_dir($dir)){
			throw new \Exception('目录类型错误');
		}
		$fh = opendir($dir);
		while (($file = readdir($fh)) !== false){
			if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;
			$filepath = $dir.DIRECTORY_SEPARATOR.$file;
			if($isChild==true && (in_array('*',$exts) || in_array('folder',$exts)) && is_dir($filepath)){
				$files = array_merge($files,self::getDirFileList($filepath,$exts,$sort,$isChild));
			}
			if(in_array('*',$exts) || (in_array('folder',$exts) && is_dir($filepath)) || ((in_array('file',$exts) || in_array(self::getExtName($filepath),$exts)) &&is_file($filepath) )){
				array_push($files,$filepath);
			}
		}
		closedir($fh);
		switch ($sort){
			case 'asc':
				sort($files);
				break;
			case 'desc':
				rsort($files);
				break;
			case 'nat':
				natcasesort($files);
				break;
		}
		return $files;
	}
	
	
	/**
	 * 列出指定目录下符合条件的文件和文件夹info信息
	 * @param string $dir 路径
	 * @param array $exts 需要列出的后缀名文件
	 * @param string $sort 数组排序
	 * @param boolean $isChild 是否遍历子目录
	 * @return array
	 */
	public static function getDirInfoList(string $dir,array $exts=['*'],string $sort='asc',bool $isChild=true){
		$dir = self::pathReplace($dir);
		$sort = strtolower($sort);
		$files = [];
		if(!is_dir($dir)){
			throw new \Exception('目录类型错误');
		}
		$fh = opendir($dir);
		while (($file = readdir($fh)) !== false){
			if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;
			$filepath = $dir.DIRECTORY_SEPARATOR.$file;
			if($isChild==true && (in_array('*',$exts) || in_array('folder',$exts)) && is_dir($filepath)){
				$files = array_merge($files,self::getDirInfoList($filepath,$exts,$sort,$isChild));
			}
			if(in_array('*',$exts) || (in_array('folder',$exts) && is_dir($filepath)) || ((in_array('file',$exts) || in_array(self::getExtName($filepath),$exts)) &&is_file($filepath) )){
				array_push($files, self::info($filepath));
			}
		}
		closedir($fh);
		switch ($sort){
			case 'asc':
				$last_names = array_column($files,'pathname');
				array_multisort($last_names,SORT_ASC,$files);
				break;
			case 'desc':
				$last_names = array_column($files,'pathname');
				array_multisort($last_names,SORT_DESC,$files);
				break;
		}
		return $files;
	}
	
	/**
	 * 创建文件
	 *
	 * @param string $path 路径
	 * @param string $content 内容
	 * @param int $mode 访问权限
	 * @return boolean
	 */
	public static function createFile(string $path='',string $content='',int $mode=0777){
		$dir = self::pathReplace($path);
		//路径为空或文件存在 返回false
		if(!$path || is_file($path) || is_dir($path)){
			return false;
		}
		$dir = pathinfo($path);
		//判断目录是否存在
		if (!file_exists($dir['dirname'])){
			self::createDir($dir['dirname'], $mode);
		}
		return file_put_contents($path,$content) !== false;
	}
	
	/**
	 * 拷贝/移动文件
	 *
	 * @param string $oldPath 原路径
	 * @param string $newPath 新路径
	 * @param string $type 类型(copy 拷贝 move 移动)
	 * @param boolean $overWrite 是否覆盖
	 * @return boolean
	 */
	public static function handleFile(string $oldPath='',string $newPath='',string $type='copy',bool $overWrite=false){
		$oldPath = self::pathReplace($oldPath);
		$newPath = self::pathReplace($newPath);
		if(!$oldPath || !$newPath || !is_file($oldPath)){
			return false;
		}
		if(is_file($newPath) && $overWrite==false){
			return false;
		}
		if(is_file($newPath) && $overWrite==true){
			self::delFile($newPath);
		}
		$path_parts = pathinfo($newPath);
		self::createDir($path_parts['dirname'], true, 0777);
		switch ($type){
			case 'copy':
				return copy($oldPath,$newPath);
				break;
			case 'move':
				return self::repeatName($oldPath,$newPath);
				break;
			default:
				throw new \Exception('类型错误');
		}
	}
	
	/**
	 * 删除文件
	 *
	 * @param string $path 路径
	 * @return boolean
	 */
	public static function delFile(string $path=''){
		$path = self::pathReplace($path);
		if(!$path || !is_file($path)){
			return false;
		}
		return unlink($path);
	}
	
	/**
	 * 文件指定编码转换
	 * 
	 * @param string $path 文件路径
	 * @param string $oldCode 原始编码
	 * @param string $newCode 输出编码
	 * @return boolean
	 */
	public static function fileChangeCode(string $path,string $oldCode,string $newCode){
		if(!$oldCode || !$newCode){
			throw new \Exception('编码类型错误');
		}
		$path = self::pathReplace($path);
		if(!is_file($path)){
			throw new \Exception('文件参数错误');
		}
		try {
			$content = file_get_contents($path);
			$content = \thinkmanage\tools\Str::changCode($content,$oldCode,$newCode);
			$fp = fopen($path,'w');
			$res = fputs($fp,$content) ? true : false;
			fclose($fp);
			return $res;
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}
	
	/**
	 * 文件重命名
	 * @param string $oldname
	 * @param string $newname
	 * @return boolean
	 */
	public static function repeatName(string $oldName,string $newName){
		$oldName = self::pathReplace($oldName);
		$newName = self::pathReplace($newName);
		if(($newName!=$oldName) && is_writable($oldName)){
			return rename($oldName,$newName);
		}
		return false;
	}
	
	/**
	 * 文件大小
	 * 
	 * @param int $file 文件
	 * @param int $isFormat 返回格式化数据
	 * @param int $dec 小数标记
	 * @return int|string
	 */
	public static function fileSize(string $file,bool $isFormat=false,int $dec=2){
		$file = self::pathReplace($file);
		if(!is_file($file)){
			throw new \Exception('文件参数错误');
		}
		return $isFormat?self::fileFormat(filesize($file),$dec):filesize($file);
	}
	
	/**
	 * 文件大小格式化
	 * 把字节数格式为格式化描述
	 * 
	 * @param int $size 大小
	 * @param int $dec 小数标记
	 * @return int
	 */
	public static function fileFormat(string $size,int $dec=2){
		$formatList = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
		$pos = 0;
		while ($size >= 1024){
			 $size /= 1024;
			 $pos++;
		}
		return round($size,$dec)." ".$formatList[$pos];
	}
	
	
	
	/**
	 * 格式化路径
	 * 将路径中的/,//,\,\\等替换成服务器支持的路径分割符
	 * @param string $path 路径
	 * @return string
	 */
	public static function pathReplace(string $path){
		return rtrim(str_replace(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,
			str_replace('//','/',
				str_replace('\\','/',$path)
			)
		),DIRECTORY_SEPARATOR);
	}
	
	/**
     * 获取完整文件名
     * @param string $path 路径
     * @return string
     */
    public static function getBaseName(string $path){
        $path = self::pathReplace($path);
        return basename($path);
    }
    
    /**
     * 获取文件后缀名
     * @param string $file_name 文件路径
     * @return string
     */
    public static function getExtName(string $file){
        $file = self::pathReplace($file);
        return pathinfo($file,PATHINFO_EXTENSION);
    }
}