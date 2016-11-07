<!DOCTYPE HTML>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>URL helper class sample file</title>
        <style type="text/css">
            body{
                background-color:#eee;
                padding-bottom:50px;
                width:960px;
                margin:0 auto;
            }
            table {
                border-collapse: collapse;
                background-color: #f8f8f8;
            }
            td{
                border:1px solid #aaa;
                border-bottom:2px solid #888;
                padding:5px 10px;
            }
            .section{
                text-align:center;
                font-weight:bold;
                padding:10px;
                background-color:#666;
                color:#eee;
                border:1px solid #aaa;
            }
            .code{                
                border-bottom:1px solid #aaa;
            }
            code{
                font-family:monospace;
            }
            .note{
                font-style:italic;                
            }
        </style>
        </head>
    <body>
        <?php
        ini_set('display_errors',1);
        require_once(ROOT."url.php");
        ?>
        <h1>URL - introduction</h1>
        
        <p>Welcome! This is a URL helper class, that simplifies the tasks of 
            handling urls. The class can either be used as a 
            <a href="#static">static class</a> but it can be 
            <a href="#instance">instantiated</a> too.</p>
        
        <h2 id="static">Static method</h2>
        <table>
            <tr>
                <td colspan="2" class="section">Basic usage</td>
            </tr>
            
            <tr>
                <td class="code"><code>URL::get()</code></td>
                <td rowspan="2"><?php global $url; echo $url->get(); ?></td>
            </tr>
            <tr>
                <td class="note">get the current url</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::add('x',1)</code></td>
                <td rowspan="2"><?php global $url; echo $url->add('x',1); ?></td>
            </tr>
            <tr>
                <td class="note">add argument x=1</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::add(Array('x'=>1,'y'=>2))</code></td>
                <td rowspan="2"><?php global $url; echo $url->add(Array('x'=>1,'y'=>2)); ?></td>
            </tr>
            <tr>
                <td class="note">add arguments x=1, y=2</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::add(Array('x'=>Array('a'=>1, 'b'=>'foo')))</code></td>
                <td rowspan="2"><?php global $url; echo $url->add(Array('x'=>Array('a'=>1, 'b'=>'foo'))) ?></td>
            </tr>
            <tr>
                <td class="note">add array as paramter</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::path('path/to/stuff')</code></td>
                <td rowspan="2"><?php global $url; echo $url->path('path/to/stuff'); ?></td>
            </tr>
            <tr>
                <td class="note">set path as string</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::path(Array('path','to','stuff'))</code></td>
                <td rowspan="2"><?php global $url; echo $url->path(Array('path','to','stuff')); ?></td>
            </tr>
            <tr>
                <td class="note">set path as array</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::path('path','to','stuff')</code></td>
                <td rowspan="2"><?php global $url; echo $url->path('path','to','stuff'); ?></td>
            </tr>
            <tr>
                <td class="note">set path with arguments</td>                
            </tr>
            
             <tr>
                <td class="code"><code>URL::file('path','to','stuff.txt')</code></td>
                <td rowspan="2"><?php global $url; echo $url->file('path','to','stuff.txt'); ?></td>
            </tr>
            <tr>
                <td class="note">set file with arguments</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::canonical()</code></td>
                <td rowspan="2"><?php global $url; echo $url->canonical(); ?></td>
            </tr>
            <tr>
                <td class="note">get the canonical link for current url</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::args()</code></td>
                <td rowspan="2"><?php global $url; print_r($url->args()); ?></td>
            </tr>
            <tr>
                <td class="note">get the arguments</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::args(Array('x'=>1,'y'=>2))</code></td>
                <td rowspan="2"><?php global $url; echo $url->args(Array('x'=>1,'y'=>2)); ?></td>
            </tr>
            <tr>
                <td class="note">set the arguments (same syntax as at URL::add)</td>                
            </tr>  
            
            <tr>
                <td class="code"><code>URL::hash('test')</code></td>
                <td rowspan="2"><?php global $url; echo $url->hash('test'); ?></td>
            </tr>
            <tr>
                <td class="note">set hash</td>                
            </tr>            
            
            <tr>
                <td colspan="2" class="section">Passing base</td>
            </tr>
            
            <tr>
                <td class="code"><code>$url = URL::file('path','to','file.html')</code></td>
                <td rowspan="2"><?php global $url; echo $url->file('path','to','file.html'); ?></td>
            </tr>
            <tr>
                <td class="note">set file</td>                
            </tr>
            <tr>
                <td class="code"><code>$url = URL::add(Array('x'=>1,'y'=>2),false,$url)</code></td>
                <td rowspan="2"><?php global $url; echo $url->add(Array('x'=>1,'y'=>2),false,$url); ?></td>
            </tr>
            <tr>
                <td class="note">add arguments x=1, y=2, store results in $url</td>                
            </tr>
            <tr>
                <td class="code"><code>$url = URL::add('z',3,$url)</code></td>
                <td rowspan="2"><?php global $url; echo $url->add('z',3,$url); ?></td>
            </tr>
            <tr>
                <td>add z = 3 to previously stored $url</td>                
            </tr>
            <tr>
                <td class="code"><code>$url = URL::remove('y',$url)</code></td>
                <td rowspan="2"><?php global $url; echo $url->remove('y',$url); ?></td>
            </tr>
            <tr>
                <td class="note">remove y from previously stored $url</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::args(false, false, $url)</code></td>
                <td rowspan="2"><?php global $url; print_r($url->args(false, false, $url)); ?></td>
            </tr>
            <tr>
                <td class="note">get the arguments from $url</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::hash('test',$url)</code></td>
                <td rowspan="2"><?php global $url; echo $url->hash('test',$url); ?></td>
            </tr>
            <tr>
                <td class="note">set hash for $url<br />(note: the hash does not get stored in the $url)</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::canonical($url)</code></td>
                <td rowspan="2"><?php global $url; echo $url->canonical($url); ?></td>
            </tr>
            <tr>
                <td class="note">get the canonical link for $url</td>                
            </tr>
            
            <tr>
                <td class="code"><code>URL::canonical($url, true)</code></td>
                <td rowspan="2"><?php global $url; echo $url->canonical($url, true); ?></td>
            </tr>
            <tr>
                <td class="note">get the canonical link for $url w/ args</td>                
            </tr>
        </table>
        
        
        <h2 id="instance">Instance method</h2>
        
        <table>
            <tr>
                <td class="code"><code>$url = new URL()</code></td>
                <td rowspan="2"><?php $url = new URL() ?>-</td>
            </tr>
            <tr>
                <td class="note">create url object</td>                
            </tr>
            
            <tr>
                <td class="code"><code>echo $url</code></td>
                <td rowspan="2"><?php global $url; echo $url ?>
            </tr>
            <tr>
                <td class="note">print url</td>                
            </tr>
            
            <tr>
                <td class="code"><code>$url->add('x',1)</code></td>
                <td rowspan="2"><?php echo $url->add('x',1) ?></td>
            </tr>
            <tr>
                <td class="note">add x=1</td>                
            </tr>
            
            <tr>
                <td class="code"><code>$url->add(Array('y'=>'stuff','z'=>3))</code></td>
                <td rowspan="2"><?php echo $url->add(Array('y'=>'stuff','z'=>3)) ?></td>
            </tr>
            <tr>
                <td class="note">add y=stuff & z=3</td>                
            </tr>
            
            <tr>
                <td class="code"><code>$url->add(Array('x'=>Array('a'=>1, 'b'=>'foo')))</code></td>
                <td rowspan="2"><?php echo $url->add(Array('x'=>Array('a'=>1, 'b'=>'foo'))) ?></td>
            </tr>
            <tr>
                <td class="note">add array as paramter</td>                
            </tr>
            
            <tr>
                <td class="code"><code>$url->args()</code></td>
                <td rowspan="2"><?php print_r($url->args()) ?></td>
            </tr>
            <tr>
                <td class="note">get args</td>                
            </tr>
            
            <tr>
                <td class="code"><code>$url->remove('z')</code></td>
                <td rowspan="2"><?php echo $url->remove('z') ?></td>
            </tr>
            <tr>
                <td class="note">remove z</td>                
            </tr>
            
            <tr>
                <td class="code"><code>$url->remove()</code></td>
                <td rowspan="2"><?php echo $url->remove() ?></td>
            </tr>
            <tr>
                <td class="note">remove all args</td>                
            </tr>
            
            <tr>
                <td class="code"><code>$url->path('path','to','stuff')</code></td>
                <td rowspan="2"><?php echo $url->path('path','to','stuff') ?></td>
            </tr>
            <tr>
                <td class="note">set path</td>                
            </tr>
            
            <tr>
                <td class="code"><code>$url->file('path','to','stuff.html')</code></td>
                <td rowspan="2"><?php echo $url->file('path','to','stuff.html') ?></td>
            </tr>
            <tr>
                <td class="note">set file path</td>                
            </tr>
            
            <tr>
                <td class="code"><code>$url->basepath('base/folder')</code></td>
                <td rowspan="2"><?php echo $url->basepath('base/folder') ?></td>
            </tr>
            <tr>
                <td class="note">set base path</td>                
            </tr>
            
            <tr>
                <td class="code"><code>$url->hash('test')</code></td>
                <td rowspan="2"><?php echo $url->hash('test'); ?></td>
            </tr>
            <tr>
                <td class="note">set hash</td>                
            </tr>
            
            <tr>
                <td class="code"><code>$url->canonical()</code></td>
                <td rowspan="2"><?php echo $url->canonical(); ?></td>
            </tr>
            <tr>
                <td class="note">canonical link</td>                
            </tr>
            
        </table>
        
        <p style="margin-top:50px;">The functions are the same for the two styles. For further info, check
            the documentation in the source, or generate the doc with 
            <a href="http://www.stack.nl/~dimitri/doxygen/" target="_blank">Doxygen</a>.</p>
        
    </body>
</html>
