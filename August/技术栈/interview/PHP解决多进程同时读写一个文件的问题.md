## PHP解决多进程同时读写一个文件的问题

首先我们要明确一个问题，PHP是支持进程而不支持多线程，如果说对于文件操作，其实你只要给文件枷锁就能解决
不需要其他的操作，PHP的flock已经帮你搞定了。


```php

//用flock在写文件前先锁上，等写完后解锁，这样就实现了多线程同时读写一个文件避免冲突。大概就是下面这个流程




/*
*flock(file,lock,block)
*file 必需，规定要锁定或释放的已打开的文件
*lock 必需。规定要使用哪种锁定类型。
*block 可选。若设置为 1 或 true，则当进行锁定时阻挡其他进程。
*lock
*LOCK_SH 要取得共享锁定（读取的程序）
*LOCK_EX 要取得独占锁定（写入的程序）
*LOCK_UN 要释放锁定（无论共享或独占）
*LOCK_NB 如果不希望 flock() 在锁定时堵塞
*/

if (flock($file, LOCK_EX)){

	fwrite($file, "write words");

	flock($file, LOCK_UN);

}else{

	//处理错误逻辑

}

fclose($file);

```

四种高并发读写文件的方案 

一、一般方案

一般的解决方案时当一进程对文件进行操作时，首先对其它进行加锁，意味着这里只有该进程有权对文件进行读取，其它进程如果现在读，是完全没有问题，但如果这时有进程试图想对其进行更新，会遭到操作拒绝，先前对文件进行加锁的进程这时如果对文件的更新操作完毕，这就释放独占的标识，这时文件又恢复到了可更改的状态。接下来同理，如果那个进程在操作文件时，文件没有加锁，这时，它就可以放心大胆的对文件进行锁定，独自享用。



```php

$fp=fopen('/tmp/lock.txt','w+');
if (flock($fp,LOCK_EX)){
    fwrite($fp,"Write something here\n");
    flock($fp,LOCK_UN);
}else{
    echo 'Couldn\'t lock the file !';
}
fclose($fp);

```

但在PHP中，flock似乎工作的不是那么好！在多并发情况下，似乎是经常独占资源，不即时释放，或者是根本不释放，造成死锁，从而使服务器的cpu占用很高，甚至有时候会让服务器彻底死掉。好像在很多linux/unix系统中，都会有这样的情况发生。所以使用flock之前，一定要慎重考虑。
那么就没有解决方案了吗？其实也不是这样的。如果flock()我们使用得当，完全可能解决死锁的问题。当然如果不考虑使用flock()函数，也同样会有很好的解决方案来解决我们的问题。经过我个人的搜集和总结，大致归纳了解决方案有如下几种。

方案1 
	对文件进行加锁时，设置一个超时时间。大致实现如下：

```php
if($fp=fopen($fileName,'a')){
 $startTime=microtime();
 do{
  $canWrite=flock($fp,LOCK_EX);
  if(!$canWrite){
   usleep(round(rand(0,100)*1000));
  }
 }while((!$canWrite)&&((microtime()-$startTime)<1000));
 if($canWrite){
  fwrite($fp,$dataToSave);
 }
 fclose($fp);
}

```

超时设置为1ms，如果这里时间内没有获得锁，就反复获得，直接获得到对文件操作权为止，当然。如果超时限制已到，就必需马上退出，让出锁让其它进程来进行操作。

方案二：不使用flock函数，借用临时文件来解决读写冲突的问题。大致原理如下

1）将需要更新的文件考虑一份到我们的临时文件目录，将文件最后修改时间保存到一个变量，并为这个临时文件取一个随机的，不容易重复的文件名。
（2）当对这个临时文件进行更新后，再检测原文件的最后更新时间和先前所保存的时间是否一致。
（3）如果最后一次修改时间一致，就将所修改的临时文件重命名到原文件，为了确保文件状态同步更新，所以需要清除一下文件状态。
（4）但是，如果最后一次修改时间和先前所保存的一致，这说明在这期间，原文件已经被修改过，这时，需要把临时文件删除，然后返回false,说明文件这时有其它进程在进行操作。
实现代码如下：

```php

$dir_fileopen='tmp';
function randomid(){
    return time().substr(md5(microtime()),0,rand(5,12));
}
function cfopen($filename,$mode){
    global $dir_fileopen;
    clearstatcache();
    do{
  $id=md5(randomid(rand(),TRUE));
        $tempfilename=$dir_fileopen.'/'.$id.md5($filename);
    } while(file_exists($tempfilename));
    if(file_exists($filename)){
        $newfile=false;
        copy($filename,$tempfilename);
    }else{
        $newfile=true;
    }
    $fp=fopen($tempfilename,$mode);
    return $fp?array($fp,$filename,$id,@filemtime($filename)):false;
}
function cfwrite($fp,$string){
 return fwrite($fp[0],$string);
}
function cfclose($fp,$debug='off'){
    global $dir_fileopen;
    $success=fclose($fp[0]);
    clearstatcache();
    $tempfilename=$dir_fileopen.'/'.$fp[2].md5($fp[1]);
    if((@filemtime($fp[1])==$fp[3])||($fp[4]==true&&!file_exists($fp[1]))||$fp[5]==true){
        rename($tempfilename,$fp[1]);
    }else{
        unlink($tempfilename);
  //说明有其它进程 在操作目标文件，当前进程被拒绝
        $success=false;
    }
    return $success;
}
$fp=cfopen('lock.txt','a+');
cfwrite($fp,"welcome to beijing.\n");
fclose($fp,'on');

```

对于上面的代码所使用的函数，需要说明一下：
（1）rename();重命名一个文件或一个目录，该函数其实更像linux里的mv。更新文件或者目录的路径或名字很方便。但当我在window测试上面代码时，如果新文件名已经存在，会给出一个notice,说当前文件已经存在。但在linux下工作的很好。
（2）clearstatcache();清除文件的状态.php将缓存所有文件属性信息，以提供更高的性能，但有时，多进程在对文件进行删除或者更新操作时，php没来得及更新缓存里的文件属性，容易导致访问到最后更新时间不是真实的数据。所以这里需要使用该函数对已保存的缓存进行清除。
