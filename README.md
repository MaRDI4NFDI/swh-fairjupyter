# Software Heritage API-Client demo for FAIR Jupyter knowledge graph

[FAIR Jupyter](https://fusion-jena.github.io/fairjupyter/) is a knowledge graph representation of [Dataset of a Study of Computational reproducibility of Jupyter notebooks from biomedical publications](https://doi.org/10.5281/zenodo.8226725). The knowledge graph can be queried, e.g. for [GitHub repositories associated with the Jupyter notebooks in the dataset](https://reproduceme.uni-jena.de/#/dataset/fairjupyter/query?query=%23%20List%20of%20GitHub%20repositories%20covered%20by%20https%3A%2F%2Fdoi.org%2F10.1093%2Fgigascience%2Fgiad113%20%0A%0ASELECT%20DISTINCT%20%0A%3Frepo_url_base%0AWHERE%20%7B%0A%20%20%3Frepository%20%3Chttps%3A%2F%2Fw3id.org%2Freproduceme%2Furl%3E%20%3Frepo_url_base%20.%0A%7D%0AORDER%20BY%20ASC%28%3Frepo_url_base%29), from which the file [FIZ/queryResults.csv](FIZ/queryResults.csv) was generated.

## Installation Steps:

    1) Clone this project.
    
    2) Open a console session and navigate to the cloned directory:
    
        Run "composer install"

        This should involve installing the PHP REPL, PsySH

    3) Acquire SWH tokens for increased SWH-API Rate-Limits.
    
    4) Prepare .env file and add tokens:   
    
        4.1) Rename/Copy the cloned ".env.example" file to .env
                cp .env.example .env   
                
        4.2) (Optional) Edit these two token keys:
        
                SWH_TOKEN_PROD=Your_TOKEN_FROM_SWH_ACCOUNT                   # step 3)                 
                SWH_TOKEN_STAGING=Your_STAGING_TOKEN_FROM_SWH_ACCOUNT   # step 3)                 

## Quickstart:

In a console session inside the cloned directory, start the archival:

```php
php -r "require 'Experiments.php'; require 'vendor/autoload.php'; FIZ\Experiments::archiveTest();"
```
This generates the a file named requestIDs.txt.
To retrieve the archived ids, run:
```php
php -r "require 'Experiments.php'; require 'vendor/autoload.php'; FIZ\Experiments::getSwhIDs();"
```

notes:
Sometimes, swh doesn't complete a given archival process (their internal work) but yet they send that it has succeeded.

So we had to modify the code to assume that SWH may report success but without generated IDs given (in the very same json response) or gives partial data in the deeply nested nodes.

In the swhIDs.txt, one can find all the successfully archived and tracked repositories. 5122 succeeded out of 5154 in total.

In the requestIDs-failed.txt, you can find those that have failed by SWH. There's no reason given by SWH. We repeated the archival request but yet swh reported "failed" again.

In the requestIDs-2ndRun.txt, that was just the remaining URLs from the failed run.

> More details: [Archive](https://github.com/Ramy-Badr-Ahmed/swh-client/wiki).

