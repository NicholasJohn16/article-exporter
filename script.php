<?php 

$path    = $console->getHelperSet()->get('dialog')
    ->ask($output, '<info>Enter the path to where you want to export articles to ? ', __DIR__.'/articles');

if ( !file_exists($path) )
{
    if ( !mkdir($path, 0755) ) {
        throw new \RuntimeException("Unable to create the directory $path");
    }    
}

$cats = \KService::get('repos:contents.category', array(
        'resources' => 'categories'
));

$sections = \KService::get('repos:contents.section', array(
    'resources' => 'sections'
));

$artilces = \KService::get('repos:contents.article', array(
   'resources' => 'content',
   'relationships' => array(
        'section'  => array('child_column'=>'sectionid'),
        'category' => array('child_column'=>'catid')           
    )     
));

foreach($artilces->fetchSet() as $article) 
{
    $base = $path;
    if ( isset($article->category) )
    {
        $base = $base.'/'.str_replace('-','_',$article->category->alias);
        if ( !file_exists($base) &&
                !mkdir($base, 0755) ) {
            throw new \RuntimeException("Unable to create the directory $base");
        }
    }    
    if ( isset($article->section) ) 
    {        
        $base = $base.'/'.str_replace('-','_',$article->section->alias);
        if ( !file_exists($base) && 
                !mkdir($base, 0755) ) {
            throw new \RuntimeException("Unable to create the directory $base");
        }
    }    
    $title     = $article->title;
    $filename  = str_replace('-','_',$article->alias).'.php';
    $text      = $article->introtext;
    $content   = <<<EOF
<?php @title('$title') ?>
$text
EOF;
    file_put_contents($base.'/'.$filename, $content); 
}


?>
