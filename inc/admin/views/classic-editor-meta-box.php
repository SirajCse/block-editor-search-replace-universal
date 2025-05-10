<div class="besnr-admin">
	<div class="besnr-form-ui">
		<form id="besnr-form" name="besnr-form" type="post">
			<div class="besnr-loading-bar"></div>
			<div id="besnr-output" class="besnr-output"></div>
			<input
				type="hidden"
				id="besnr-current-post-type"
				name="besnr-current-post-type"
				value="<?php echo esc_attr( $post->post_type ); ?>"
			/>
			<input
				type="hidden"
				id="besnr-current-post-id"
				name="besnr-current-post-id"
				value="<?php echo esc_attr( $post->ID ); ?>"
				/>
			<input
				type="hidden"
				id="besnr-classic-editor"
				name="besnr-classic-editor"
				value="1" 
			/>
			<div class="search-replace-group">
				<p><?php echo esc_html( 'Easily find and replace anything in the Block Editor with our search and replace plugin.', 'block-editor-search-replace' ); ?></p>
				<p>
					<label for="besnr-highlight-search">
						<input
							type="checkbox"
							id="besnr-highlight-search"
							name="besnr-highlight-search"
							checked
						/>
						<span><?php echo esc_html( 'Highlight search results', 'block-editor-search-replace' ); ?></span>
					</label>
				</p>
				<p>
					<label for="besnr-case-sensitive">
						<input
							type="checkbox"
							id="besnr-case-sensitive"
							name="besnr-case-sensitive"
						/>
						<span><?php echo esc_html( 'Case sensitive search & replace', 'block-editor-search-replace' ); ?></span>
					</label>
				</p>
				<p>
					<select id="besnr-search-method" name="besnr-search-method" class="besnr-search-method">
						<option value="text"><?php echo esc_html( 'Text-only', 'block-editor-search-replace' ); ?></option>
						<option value="url"><?php echo esc_html( 'URLs', 'block-editor-search-replace' ); ?></option>
						<option value="image"><?php echo esc_html( 'Images', 'block-editor-search-replace' ); ?></option>
						<option value="multiple"><?php echo esc_html( 'Multiple Terms', 'block-editor-search-replace' ); ?></option>
					</select>
				</p>
				<p>
					<textarea
						id="besnr-search-input"
						name="besnr-search-input"
						class="besnr-search-input"
						placeholder="<?php echo esc_html( 'Enter your search phrase...', 'block-editor-search-replace' ); ?>"
					></textarea>
				</p>
				<button
					type="button"
					id="besnr-preview-changes"
					name="besnr-preview-changes"
					class="button button-primary besnr-preview-changes"
					style="display: none;"
				>
					<i class="dashicons dashicons-desktop"></i>
					<?php echo esc_html( 'Preview changes', 'block-editor-search-replace' ); ?>
				</button>
				<p>
					<textarea
						id="besnr-replace-with-input"
						name="besnr-replace-with-input"
						class="besnr-replace-with-input"
						placeholder="<?php echo esc_html( 'Enter your replace with phrase..', 'block-editor-search-replace' ); ?>."
					></textarea>
				</p>
				<p id="besnr-button-group" class="button-group">
					<button
						type="button" 
						id="besnr-default-search-replace" 
						name="besnr-default-search-replace" 
						class="button button-primary besnr-default-search-replace"
					>
						<i class="dashicons dashicons-randomize"></i>
						<?php echo esc_html( 'Replace', 'block-editor-search-replace' ); ?>
					</button>
					<button
						type="button" 
						id="besnr-remove-highlight-tags" 
						name="besnr-remove-highlight-tags" 
						class="button"
					>
						<?php echo esc_html( 'Remove tags', 'block-editor-search-replace' ); ?>
					</button>
				</p>
				<p>
					<hr />
					<span>
						<?php echo esc_html( '• Search action will fire automatically as you leave the input field.', 'block-editor-search-replace' ); ?>
					</span>
				</p>
			</div>
		</form>
	</div>
</div>
