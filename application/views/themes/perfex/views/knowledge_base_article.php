	<div class="panel_s">
		<div class="panel-body">
			<div class="row kb-article">
				<div class="col-md-<?php if(count($related_articles) == 0){echo '12';}else{echo '8';} ?>">
					<h1 class="bold no-mtop kb-article-single-heading"><?php echo $article->subject; ?></h1>
					<hr class="no-mtop" />
					<div class="mtop10 tc-content">
						<?php echo $article->description; ?>
					</div>
					<hr />
					<h4 class="mtop20"><?php echo _l('clients_knowledge_base_find_useful'); ?></h4>
					<div class="answer_response"></div>
					<div class="btn-group mtop15 article_useful_buttons" role="group">
						<input type="hidden" name="articleid" value="<?php echo $article->articleid; ?>">
						<button type="button" data-answer="1" class="btn btn-success"><?php echo _l('clients_knowledge_base_find_useful_yes'); ?></button>
						<button type="button" data-answer="0" class="btn btn-danger"><?php echo _l('clients_knowledge_base_find_useful_no'); ?></button>
					</div>
				</div>
				<?php if(count($related_articles) > 0){ ?>
				<div class="col-md-4">
					<h4 class="bold no-mtop h3 kb-related-heading"><?php echo _l('related_knowledgebase_articles'); ?></h4>
					<hr class="no-mtop" />
					<ul class="mtop10 articles_list">
						<?php foreach($related_articles as $article) { ?>
						<li>
							<a href="<?php echo site_url('knowledge_base/'.$article['slug']); ?>" class="article-heading"><?php echo $article['subject']; ?></a>
							<div class="text-muted mtop10"><?php echo strip_tags(mb_substr($article['description'],0,100)); ?>...</div>
						</li>
						<hr />
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php

