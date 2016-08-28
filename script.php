<?php 
$template = KService::get('repos://site/templates.menu', array(
        				'resources'         => 'templates_menu',
        				'identity_property' => 'menuid'
        		))->getQuery()->clientId(0)->fetchValue('template');

$path    = WWW_ROOT.'/templates/'.$template.'/html/com_html/content';
$dialog  = $console->getHelperSet()->get('dialog');
if ( !$dialog->askConfirmation($output, '<info>Export articles into '.$template.' template ? (y\n) </info>') ) {
    do {
        $path = $dialog->ask($output, '<info>Enter the path to where you want to export articles to ? ');
    } while( empty($path) );    
}
if ( !file_exists($path) )
{
    if ( !mkdir($path, 0755) ) {
        throw new \RuntimeException("Unable to create the directory $path");
    }    
}

class ComContentsDomainEntityCategory extends AnDomainEntityDefault
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'id' => array(
                    'key' => true,
                    'type' => 'integer',
                    'read' => 'public',
                )
            )
        ));

        parent::_initialize($config);
    }
}

class ComContentsDomainEntitySection extends AnDomainEntityDefault
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'id' => array(
                    'key' => true,
                    'type' => 'integer',
                    'read' => 'public',
                )
            )
        ));

        parent::_initialize($config);
    }
}

class ComContentsDomainEntityArticle extends AnDomainEntityDefault
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'id' => array(
                    'key' => true,
                    'type' => 'integer',
                    'read' => 'public',
                )
            )
        ));

        parent::_initialize($config);
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
    $output->writeLn('Exporting '.$article->title.'...');
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
