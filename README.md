<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# About Laravel Elastic Search (Ubuntu)

## Install Necessary Dependencies
Step 1: Since Elasticsearch runs on top of Java, you need to install the Java Development Kit (JDK).    
Run this command in terminal :

    java -version

Step 2: If you do not have Java installed then install default JDK, run the following command:

    sudo apt-get update
    sudo apt install openjdk-8-jdk

Step 3: Run this command in terminal : 
                    
    java -version

Step 4: To allow access to your repositories via HTTPS, you need to install an APT transport package:

    sudo apt install apt-transport-https

## Install and Download Elasticsearch on Ubuntu

First, update the GPG key for the Elasticsearch repository.
Use the wget command to pull the public key:

    wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -

The output should display `OK` if everything went as it should.

## Install Elasticsearch

Step 1: Run this command in terminal :

    sudo apt-get update
    sudo apt install elasticsearch

Step 2: Start Elasticsearch Service
    sudo systemctl daemon-reload

Then, enable the Elasticsearch service with:

    sudo systemctl enable elasticsearch.service

And finally, after the service is enabled, start Elasticsearch:

    sudo systemctl start elasticsearch.service

If you make changes to configuration files, or need to restart Elasticsearch for any reason, use:

    sudo systemctl restart elasticsearch.service

When you need to stop the service, use the following command:

    sudo systemctl stop elasticsearch.service

Step 3: Check Elasticsearch Status

    service elasticsearch status
    OR
    systemctl status elasticsearch.service 

<br>
If you're on  Ubuntu, sometimes the systemctl commands won't work. Instead, use the following commands to start, stop and restart the Elasticsearch service:

    sudo service elasticsearch start
    sudo service elasticsearch stop
    sudo service elasticsearch restart

## How to implement elastic search

Step 1: You have to install dependency of elastic search

    composer require elasticquent/elasticquent

Step 2: You need to register Laravel service provider, in your `config/app.php`

    'providers' => [
        Elasticquent\ElasticquentServiceProvider::class,
    ],

    'aliases' => [
        'Es' => Elasticquent\ElasticquentElasticsearchFacade::class,
    ],

Step 3: By default, Elasticquent will connect to `localhost:9200` and use `default as index` name, you can change this and the other settings in the configuration file. You can add the `elasticquent.php`

    php artisan vendor:publish --provider="Elasticquent\ElasticquentServiceProvider"
Code in `config/elasticquent.php`

    <?php

        $elasticsearch_host = env('ELASTICSEARCH_HOST', 'localhost');
        $elasticsearch_port = env('ELASTICSEARCH_PORT', '9200');
    
        return array(
    
        /*
        |--------------------------------------------------------------------------
        | Custom Elasticsearch Client Configuration
        |--------------------------------------------------------------------------
        |
        | This array will be passed to the Elasticsearch client.
        | See configuration options here:
        |
        | http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/current/_configuration.html
        */
    
        'config' => [
            'hosts'     => [ "{$elasticsearch_host}:{$elasticsearch_port}" ],
            'retries'   => 1,
        ],
    
        /*
        |--------------------------------------------------------------------------
        | Default Index Name
        |--------------------------------------------------------------------------
        |
        | This is the index name that Elasticquent will use for all
        | Elasticquent models.
        */
    
        'default_index' => env('ELASTICQUENT_DEFAULT_INDEX', 'elasticquent'),
    
        );


Step 2: First you have to create migration and model of table

    php artisan make:model Article -m

Step 3: You have to define use of elastic search in model which we are using for searching or indexing.

i.g. In Article model


    use Elasticquent\ElasticquentTrait;

    class Article extends Model 
    { 
        use ElasticquentTrait;
    }


Step 4: You have to store bulk of data around 1,00,000 in your database using faker or seeder.
        
    $faker = Factory::create();

    for ($i = 0; $i < 100000; $i++) {
        
        # Craete new object and save data
        $article = new Article();
        $article->title = $faker->sentence(3);
        $article->body = $faker->paragraph(6);
        $article->tags =  join(',',$faker->words(4));
        $article->save();

        # Add index for searching
        $article->addToIndex();
    }

Step 5: You have to do mapping in model

    protected $mappingProperties = array(
        'title' => [
          'type' => 'text',
          'analyzer' => 'standard'
        ],
        'body' => [
            'type' => 'text',
            'analyzer' => 'standard'
        ],
        'tags' => [
            'type' => 'text',
            'analyzer' => 'standard'
        ],
    );

Step 6: If you want to do elastic-search while updating/creating data then follow below code:

    public function submit(Request $request)
    {
        # Craete new object and save data
        $article = new Article();
        $article->title = $request->title;
        $article->body = $request->body;
        $article->tags = $request->tags;
        $article->save();

        # Add index for searching
        $article->addToIndex();

        # Redirect after save data
        return redirect()->route('article.index');
    }

Step 7: If you want to do elastic-search while Searching data then follow below code:

    use Elasticsearch\ClientBuilder;

    public function index(Request$request)
    {
        # Create client for elastic search
        ClientBuilder::create()->build();
        
        # Query
        $articles = Article::query();

        # If seach is found
        $keyword = $request->input('search');
        if ($keyword) {
            $articles = $articles->where('title','like',"%{$keyword}%")
                ->orWhere('body','like',"%{$keyword}%")
                ->orWhere('tags','like',"%{$keyword}%");
        }

        # Get data from articles table with pagination
        $articles =  $articles->paginate(10)->withQueryString();

        # Return data in blade file
        return view('article.index',compact('articles','keyword'));
    }
