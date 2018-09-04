


# 设计模式基于php


## 单例模式

> 模式设计是什么？初学者一开始会被这高大上的名称给唬住。而对于有丰富编程经验的老鸟来说，模式设计又是无处不在。很多接触的框架就是基于各种模式设计形成的。 简单说，在写代码的过程中一开始往往接触的是面向过程的，简单的基础的编程。这个时候我们往往追求的是代码能够实现某项功能就万事大吉。管他代码多么冗余，管他代码是否可重用，管他效率如何，能实现功能就好。但是，真正应用于实际的，更被大家采纳的是那些高效的，可重用的，便于团队开发的代码。基于这些因素，你不能像练手一样，随便命名函数名，随便放置脚本。模式设计告诉是给人们组织代码提供一种思路，实现可重用代码、让代码更容易被他人理解、保证代码可靠性。 


　　在所有模式设计中，有三种基础设计模式，单例模式，工厂模式，注册树模式，其他模式往往基于这几种模式，今天带来的是单例模式。
###  什么是单例模式？
	根据这个名称，我们很容易了解到，单例模式指的是在整个应用中只有一个对象实例的设计模式。

### 为什么要使用单例模式
	php常常和数据库打交道，如果在应用中如果频繁建立连接对象，进行new操作的话，会消耗大料的系统内存资源，这并不是我们希望看到的。再则，在团队合作项目中，单例模式可以有效避免不同程序员new自己的对象，造成人为的系统消耗。	

### 如何建立单例模式？	
	　在看到这个问题的时候，相信优秀的程序员很可能自己试着根据要求去创建单例模式，而不是坐等前人的经验。区别于其他博友告诉你什么样的模式是单例模式，我人更愿意和有面向对象编程基本经验的你考虑一下如何自己建立单例模式。
	我们首先从题目出发，单例模式是只有一个对象实例的设计模式。这一点是很让人蛋疼的。我们平常创建的类不是能创建很多对象的，就是不能创建对象的（抽象类）。要创建对象需要有类这是必须的，而且不能是抽象类。这个类要防止别人可以多次创建函数。我们自然而然考虑到了从构造函数入手。但是，每次new操作都会调用构造函数，也就是会多次创建对象实例。这和我们设计初衷相悖了。在此处务必申明构造函数为private或者protected这样才能解决这个问题。
	构造函数被申明为private或者protected这注定无法通过new的方法创建实例对象了。而且我们发现，经过这一步处理后，解决问题的前景变得明朗起来了。为什么呢？既然无法通过new方法创建对象实例，那么我们只能通过类内的方法来创建对象实例了。 这个时候我们面临一个有趣的先有鸡还是先有蛋的问题。我们往往往往是创建了对象后才调用对象的方法，而此时需要调用类里面的方法来创建对象。不受是否创建对象影响都能调用的方法的解决方案毋庸置疑那就是利用关键字--static。

	　　在类内创建静态方法完成完成什么工作呢？回归主题：确保只创建一个实例对象。如何确保只有一个呢？这很简单，if判断一下啊。存在的话直接返回，不存在自己创建一个嘛。当然这个实例对象是类的静态属性。至此，单例模式要求的功能实现完成。真的完成了么?还不算~如果有个类继承本类，将构造方法申明为public那不又坏事儿了？那有必要在构造方法前加final关键字了。


### 单例模式代码
	
```php
<?php
class Single{
    public $hash;
    static protected $ins=null;
    final protected function __construct(){
        $this->hash=rand(1,9999);
    }

    static public function getInstance(){
        if (self::$ins instanceof self) {
            return self::$ins;
        }
        self::$ins=new self();
        return self::$ins;
    } 
}

```

### 总结
	本身单例模式并不复杂，但需要深入理解。很多初学者依旧会感叹：卧槽，构造方法原来不一直是public啊~卧槽还可以不通过new创建对象啊~其实笔者想说，不管构造方法被申明为public，private还是protected，最终创建对象的时候都会调用。一直是new创建对象实例，单例模式也用new创建对象，只是换个地方而已，从类外到类内。



## 工厂模式

### 何为工厂模式

工厂模式和生产有关。生产什么呢？生产出来的是一个实例对象。通过什么设备生产？通过一个工厂类生产。怎么生产呢？工厂类调用自身静态方法来生产对象实例。

工厂模式有一个关键的构造，根据一般原则命名为Factory的静态方法，然而这只是一种原则，虽然工厂方法可以任意命名这个静态还可以接受任意数据的参数，必须返回一个对象。

### 为什么要使用工厂模式？
很多没接触过工厂模式的人会不禁问，为啥我要费那么大的劲儿去构造工厂类去创建对象呢？不去套用那些易维护，可扩展之类的话，我们可以考虑这样一个简单的问题。如果项目中，我们通过一个类创建对象。在快完成或者已经完成，要扩展功能的时候，发现原来的类类名不是很合适或者发现类需要添加构造函数参数才能实现功能扩展。我靠！我都通过这个类创建了一大堆对象实例了啊，难道我还要一个一个去改不成？我们现在才感受到了“高内聚低耦合”的博大精深。没问题，工厂方法可以解决这个问题。

　　再考虑一下，我要连接数据库，在php里面就有好几种方法，mysql扩展，mysqli扩展，PDO扩展。我就是想要一个对象用来以后的操作，具体要哪个，视情况而定喽。既然你们都是连接数据库的操作，你们就应该拥有相同的功能，建立连接，查询，断开连接...（此处显示接口的重要性）。总而言之，这几种方法应该“团结一致，一致对外”。如何实现呢？利用工厂模式。

### 工厂模式如何实现

相对于单例模式，上面我们提供了足够的信息，工厂类，工厂类里面的静态方法。静态方法里面new一下需要创建的对象实例就搞定了。当然至于考虑上面的第二个问题，根据工厂类静态方法的参数，我们简单做个判断就好了。管你用if..else..还是switch..case..，能快速高效完成判断该创建哪个类的工作就好了。最后，一定要记得，工厂类静态方法返回一个对象。不是两个，更不是三个。


### 基本的工厂类：（示例代码）

```php
//要创建对象实例的类
class MyObject{
  
}
 //工厂类
class MyFactory{
public static function factory(){
return new MyObject():
   }
}
 
 
$instance=MyFactory::factory();


```

### 靴微复杂电的工厂模式

```php
<?php

interface Transport{
    public function go();

}

class Bus implements Transport{
    public function go(){
        echo "bus每一站都要停";
    }
}

class Car implements Transport{
    public function go(){
        echo "car跑的飞快";
    }
}

class Bike implements Transport{
    public function go(){
        echo "bike比较慢";
    }
}

class transFactory{
    public static function factory($transport)
    {
        
        switch ($transport) {
            case 'bus':
                return new Bus();
                break;

            case 'car':
                return new Car();
                break;
            case 'bike':
                return new Bike();
                break;
        }
    }
}

$transport=transFactory::factory('car');
$transport->go();

```

需要工厂静态方法为factory()的时候，千万别再傻乎乎的把工厂类命名为Factory了。为啥啊？别忘了同名构造函数的事儿啊~



## 注册树模式

> 在前两篇单例模式和工厂模式后，终于迎来了最后一个基础的设计模式--注册树模式。

### 什么是注册树模式？
 注册树模式当然也叫注册模式，注册器模式。之所以我在这里矫情一下它的名称，是因为我感觉注册树这个名称更容易让人理解。像前两篇一样，我们这篇依旧是从名字入手。注册树模式通过将对象实例注册到一棵全局的对象树上，需要的时候从对象树上采摘的模式设计方法。   这让我想起了小时候买糖葫芦，卖糖葫芦的将糖葫芦插在一个大的杆子上，人们买的时候就取下来。不同的是，注册树模式摘下来还会有，能摘很多次，糖葫芦摘一次就没了。。。

### 为什么要采用注册树模式？

单例模式解决的是如何在整个项目中创建唯一对象实例的问题，工厂模式解决的是如何不通过new建立实例对象的方法。 那么注册树模式想解决什么问题呢？ 在考虑这个问题前，我们还是有必要考虑下前两种模式目前面临的局限。  首先，单例模式创建唯一对象的过程本身还有一种判断，即判断对象是否存在。存在则返回对象，不存在则创建对象并返回。 每次创建实例对象都要存在这么一层判断。 工厂模式更多考虑的是扩展维护的问题。 总的来说，单例模式和工厂模式可以产生更加合理的对象。怎么方便调用这些对象呢？而且在项目内如此建立的对象好像散兵游勇一样，不便统筹管理安排啊。因而，注册树模式应运而生。不管你是通过单例模式还是工厂模式还是二者结合生成的对象，都统统给我“插到”注册树上。我用某个对象的时候，直接从注册树上取一下就好。这和我们使用全局变量一样的方便实用。 而且注册树模式还为其他模式提供了一种非常好的想法。

### 如何实现注册树?

通过上述的描述，我们似乎很容易就找到了解决方法。首先我们需要一个作为注册树的类，这毋庸置疑。所有的对象“插入”到注册树上。这个注册树应该由一个静态变量来充当。而且这个注册树应该是一个二维数组。这个类应该有一个插入对象实例的方法（set()），当让相对应的就应该有一个撤销对象实例的方法（_unset()）。当然最重要的是还需要有一个读取对象的方法（get()）。拥有这些，我们就可以愉快地完成注册树模式啦~~~


下面让三种模式做个小小的结合。单纯创建一个实例对象远远没有这么复杂，但运用于大型项目的话，便利性便不言而喻了。

### 代码实现(code)

```php

<?php
//创建单例
class Single{
    public $hash;
    static protected $ins=null;
    final protected function __construct(){
        $this->hash=rand(1,9999);
    }

    static public function getInstance(){
        if (self::$ins instanceof self) {
            return self::$ins;
        }
        self::$ins=new self();
        return self::$ins;
    } 
}

//工厂模式
class RandFactory{
    public static function factory(){
        return Single::getInstance();
    }
}

//注册树
class Register{
    protected static $objects;
    public static function set($alias,$object){
        self::$objects[$alias]=$object;
    }
    public static function get($alias){
        return self::$objects[$alias];
    }
    public static function _unset($alias){
        unset(self::$objects[$alias]);
    }
}

Register::set('rand',RandFactory::factory());

$object=Register::get('rand');

print_r($object);


```

  至此，三种模式设计介绍完毕。各种模式设计本身就会相辅相成，往后介绍其他模式的时候，多多少少会用到一种或多种其他设计模式。


## 适配器模式

> 适配器模式着重考虑的是程序员的工作量

### 什么时候会用到适配器模式

其实最简单的例子是当我们引用一个第三方类库。这个类库随着版本的改变，它提供的API也可能会改变。如果很不幸的是，你的应用里引用的某个API已经发生改变的时候，除了在心中默默地骂“wocao”之外，你还得去硬着头皮去改大量的代码。 

　　难道真的一定要如此吗？按照套路来说，我会回答“不是的”。我们有适配器模式啊~~  

　　当接口发生改变时，适配器模式就派上了用场。

> 举个栗子

一开始的和谐

　　黑枣玩具公司专门生产玩具，生产的玩具不限于狗、猫、狮子，鱼等动物。每个玩具都可以进行“张嘴”与“闭嘴”操作，分别调用了openMouth与closeMouth方法。

　　在这个时候，我们很容易想到可以第一定义一个抽象类Toy,甚至是接口Toy,这些问题不大，其他的类去继承父类，实现父类的方法。一片和谐，信心向荣。

平衡的破坏

     为了扩大业务，现在黑枣玩具公司与红枣遥控公司合作，红枣遥控公司可以使用遥控设备对动物进行嘴巴控制。不过红枣遥控公司的遥控设备是调用的动物的doMouthOpen及doMouthClose方法。黑枣玩具公司的程序员现在必须要做的是对Toy系列类进行升级改造，使Toy能调用doMouthOpen及doMouthClose方法。

　　考虑实现的方法时，我们很直接地想到，你需要的话我再在我的父类子类里给你添加这么两个方法就好啦。当你一次又一次在父类子类里面重复添加着这两个方法的时候，总会想着如此重复的工作，难道不能解决么？当有数百个子类的时候，程序员会改疯的。程序员往往比的是谁在不影响效率的时候更会“偷懒”。这样做下去程序员会觉得自己很傻。(其实我经常当这样的傻子)

```php

abstract class Toy
{
    public abstract function openMouth();

    public abstract function closeMouth();

    //为红枣遥控公司控制接口增加doMouthOpen方法
    public abstract function doMouthOpen();

    //为红枣遥控公司控制接口增加doMouthClose方法
    public abstract function doMouthClose();
}

class Dog extends Toy
{
    public function openMouth()
    {
        echo "Dog open Mouth\n";
    }

    public function closeMouth()
    {
        echo "Dog open Mouth\n";
    }

    //增加的方法
    public function doMouthOpen()
    {
        $this->doMouthOpen();
    }

    //增加的方法
    public function doMouthClose()
    {
        $this->closeMouth();
    }
}

class Cat extends Toy
{
    public function openMouth()
    {
        echo "Cat open Mouth\n";
    }

    public function closeMouth()
    {
        echo "Cat open Mouth\n";
    }

    //增加的方法
    public function doMouthOpen()
    {
        $this->doMouthOpen();
    }

    //增加的方法
    public function doMouthClose()
    {
        $this->closeMouth();
    }
}

```

##### 更加烦躁了

程序员刚刚码完代码，喝了口水，突然间另一个消息传来。

　　黑枣玩具公司也要与绿枣遥控公司合作，因为绿枣遥控公司遥控设备更便宜稳定。不过绿枣遥控公司的遥控设备是调用的动物的operMouth(type)方法来实现嘴巴控制。如果type为0则“闭嘴”，反之张嘴。

这下好了，程序员又得对Toy及其子类进行升级，使Toy能调用operMouth()方法。搁谁都不淡定了。

```php

abstract class Toy  
{  
    public abstract function openMouth();  
  
    public abstract function closeMouth();  
  
    public abstract function doMouthOpen();  
  
    public abstract function doMouthClose();  
  
    //为绿枣遥控公司控制接口增加doMouthClose方法  
    public abstract function operateMouth($type = 0);  
}  
  
class Dog extends Toy  
{  
    public function openMouth()  
    {  
        echo "Dog open Mouth\n";  
    }  
  
    public function closeMouth()  
    {  
        echo "Dog open Mouth\n";  
    }  
  
    public function doMouthOpen()  
    {  
        $this->doMouthOpen();  
    }  
  
    public function doMouthClose()  
    {  
        $this->closeMouth();  
    }  
  
    public function operateMouth($type = 0)  
    {  
        if ($type == 0) {  
            $this->closeMouth();  
        } else {  
            $this->operateMouth();  
        }  
    }  
}  
  
class Cat extends Toy  
{  
    public function openMouth()  
    {  
        echo "Cat open Mouth\n";  
    }  
  
    public function closeMouth()  
    {  
        echo "Cat open Mouth\n";  
    }  
  
    public function doMouthOpen()  
    {  
        $this->doMouthOpen();  
    }  
  
    public function doMouthClose()  
    {  
        $this->closeMouth();  
    }  
  
    public function operateMouth($type = 0)  
    {  
        if ($type == 0) {  
            $this->closeMouth();  
        } else {  
            $this->operateMouth();  
        }  
    }  
}


```

在这个时候，程序员必须要动脑子想办法了，就算自己勤快，万一哪天紫枣青枣黄枣山枣这些遥控公司全来的时候，忽略自己不断增多的工作量不说，这个Toy类可是越来越大，总有一天程序员不崩溃，系统也会崩溃。


#### 问题处在哪呢？

像上面那样编写代码，代码实现违反了“开-闭”原则，一个软件实体应当对扩展开放，对修改关闭。即在设计一个模块的时候，应当使这个模块可以在不被修改的前提下被扩展。也就是说每个尸体都是一个小王国，你让我参与你的事情这个可以，但你不能修改我的内部，除非我的内部代码确实可以优化。

　　在这种想法下，我们懂得了如何去用继承，如何利用多态，甚至如何实现“高内聚，低耦合”。

　　回到这个问题，我们现在面临这么一个问题，新的接口方法我要实现，旧的接口（Toy抽象类）也不能动，那么总得有个解决方法吧。那就是引入一个新的类--我们本文的主角--适配器。  适配器要完成的功能很明确，引用现有接口的方法实现新的接口的方法。更像它名字描述的那样，你的接口不改的话，我就利用现有接口和你对接一下吧。 

　　到此，解决方法已经呼之欲出了，下面贴上代码。


```php

<?php
abstract class Toy  
{  
    public abstract function openMouth();  
  
    public abstract function closeMouth();  
}  
  
class Dog extends Toy  
{  
    public function openMouth()  
    {  
        echo "Dog open Mouth\n";  
    }  
  
    public function closeMouth()  
    {  
        echo "Dog close Mouth\n";  
    }  
}  
  
class Cat extends Toy  
{  
    public function openMouth()  
    {  
        echo "Cat open Mouth\n";  
    }  
  
    public function closeMouth()  
    {  
        echo "Cat close Mouth\n";  
    }  
}


//目标角色:红枣遥控公司  
interface RedTarget  
{  
    public function doMouthOpen();  
  
    public function doMouthClose();  
}  
  
//目标角色:绿枣遥控公司及  
interface GreenTarget  
{  
    public function operateMouth($type = 0);  
}


//类适配器角色:红枣遥控公司  
class RedAdapter implements RedTarget  
{  
    private $adaptee;  
  
    function __construct(Toy $adaptee)  
    {  
        $this->adaptee = $adaptee;  
    }  
  
    //委派调用Adaptee的sampleMethod1方法  
    public function doMouthOpen()  
    {  
        $this->adaptee->openMouth();  
    }  
  
    public function doMouthClose()  
    {  
        $this->adaptee->closeMouth();  
    }  
}  
  
//类适配器角色:绿枣遥控公司  
class GreenAdapter implements GreenTarget  
{  
    private $adaptee;  
  
    function __construct(Toy $adaptee)  
    {  
        $this->adaptee = $adaptee;  
    }  
  
    //委派调用Adaptee：GreenTarget的operateMouth方法  
    public function operateMouth($type = 0)  
    {  
        if ($type) {  
            $this->adaptee->openMouth();  
        } else {  
            $this->adaptee->closeMouth();  
        }  
    }  
}



class testDriver  
{  
    public function run()  
    {  
         //实例化一只狗玩具  
        $adaptee_dog = new Dog();  
        echo "给狗套上红枣适配器\n";  
        $adapter_red = new RedAdapter($adaptee_dog);  
        //张嘴  
        $adapter_red->doMouthOpen();  
        //闭嘴  
        $adapter_red->doMouthClose();  
        echo "给狗套上绿枣适配器\n";  
        $adapter_green = new GreenAdapter($adaptee_dog);  
        //张嘴  
        $adapter_green->operateMouth(1);  
        //闭嘴  
        $adapter_green->operateMouth(0);  
    }  
}  
  
$test = new testDriver();  
$test->run();

```

最后的结果就是，Toy类及其子类在不改变自身的情况下，通过适配器实现了不同的接口。

### 最后总结

	一个类的接口转换成客户希望的另外一个接口,使用原本不兼容的而不能在一起工作的那些类可以在一起工作.

###　　适配器模式核心思想：

	把对某些相似的类的操作转化为一个统一的“接口”(这里是比喻的说话)--适配器，或者比喻为一个“界面”，统一或屏蔽了那些类的细节。适配器模式还构造了一种“机制”，使“适配”的类可以很容易的增减，而不用修改与适配器交互的代码，符合“减少代码间耦合”的设计原则。



## 观察者模式

> 从面向过程的角度来看，首先是观察者向主题注册，注册完之后，主题再通知观察者做出相应的操作，整个事情就完了
> 从面向对象的角度来看，主题提供注册和通知的接口，观察者提供自身操作的接口。（这些观察者拥有一个同一个接口。）观察者利用主题的接口向主题注册，而主题利用观察者接口通知观察者。耦合度相当之低。

### 如何实现观察者注册？

> 通过前面的注册者模式很容易给我们提供思路，把这些对象加到一棵注册树上就好了嘛。如何通知？这就更简单了，对注册树进行遍历，让每个对象实现其接口提供的操作。

#### 代码实现(php)

```php
<?php
// 主题接口
interface Subject{
    public function register(Observer $observer);
    public function notify();
}
// 观察者接口
interface Observer{
    public function watch();
}
// 主题
class Action implements Subject{
     public $_observers=array();
     public function register(Observer $observer){
         $this->_observers[]=$observer;
     }

     public function notify(){
         foreach ($this->_observers as $observer) {
             $observer->watch();
         }

     }
 }

// 观察者
class Cat implements Observer{
     public function watch(){
         echo "Cat watches TV<hr/>";
     }
 } 
 class Dog implements Observer{
     public function watch(){
         echo "Dog watches TV<hr/>";
     }
 } 
 class People implements Observer{
     public function watch(){
         echo "People watches TV<hr/>";
     }
 }



// 应用实例
$action=new Action();
$action->register(new Cat());
$action->register(new People());
$action->register(new Dog());
$action->notify();
```


所谓模式，更多的是一种想法，完全没必要拘泥于代码细节。观察者模式更多体现了两个独立的类利用接口完成一件本应该很复杂的事情。不利用主题类的话，我们还需要不断循环创建实例，执行操作。而现在只需要创建实例就好，执行操作的事儿只需要调用一次通知的方法就好啦。