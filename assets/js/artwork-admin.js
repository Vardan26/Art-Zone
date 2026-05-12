(function ($) {
  $(function () {
    const $pickers = $(".art-zone-media-picker");
    const $galleryPickers = $(".art-zone-media-gallery-picker");

    if ((!$pickers.length && !$galleryPickers.length) || !wp.media) {
      return;
    }
    
    $pickers.each(function () {
      const $picker = $(this);
      let frame = null;
      const inputSelector = $picker.data("input");
      const frameTitle = $picker.data("frameTitle") || "Select image";
      const buttonText = $picker.data("buttonText") || "Use image";
      const $input = $(inputSelector);
      const $preview = $picker.find(".art-zone-media-picker__preview");

      if (!$input.length) {
        return;
      }

      const renderPreview = (url) => {
        if (!url) {
          $preview.html("");
          return;
        }

        $preview.html(`<img src="${url}" alt="" style="max-width:220px;height:auto;display:block;">`);
      };

      $picker.on("click", ".art-zone-media-picker__select", function (event) {
        event.preventDefault();

        if (frame) {
          frame.open();
          return;
        }

        frame = wp.media({
          title: frameTitle,
          button: { text: buttonText },
          multiple: false,
          library: { type: "image" },
        });

        frame.on("select", () => {
          const attachment = frame.state().get("selection").first().toJSON();
          const previewSize =
            (attachment.sizes && (attachment.sizes.medium || attachment.sizes.full)) || null;

          $input.val(String(attachment.id));
          renderPreview((previewSize && previewSize.url) || attachment.url);
        });

        frame.open();
      });

      $picker.on("click", ".art-zone-media-picker__clear", function (event) {
        event.preventDefault();
        $input.val("");
        renderPreview("");
      });
    });

    $galleryPickers.each(function () {
      const $picker = $(this);
      let frame = null;
      const inputSelector = $picker.data("input");
      const frameTitle = $picker.data("frameTitle") || "Select images";
      const buttonText = $picker.data("buttonText") || "Use images";
      const $input = $(inputSelector);
      const $preview = $picker.find(".art-zone-media-gallery-picker__preview");

      if (!$input.length) {
        return;
      }

      const renderPreview = (items) => {
        if (!items.length) {
          $preview.html("");
          return;
        }

        const html = items
          .map(
            (item) =>
              `<img src="${item.url}" alt="" style="width:92px;height:92px;object-fit:cover;display:block;">`,
          )
          .join("");

        $preview.html(html);
      };

      $picker.on("click", ".art-zone-media-gallery-picker__select", function (event) {
        event.preventDefault();

        if (frame) {
          frame.open();
          return;
        }

        frame = wp.media({
          title: frameTitle,
          button: { text: buttonText },
          multiple: true,
          library: { type: "image" },
        });

        frame.on("select", () => {
          const attachments = frame
            .state()
            .get("selection")
            .toJSON()
            .slice(0, 3);
          const ids = attachments.map((attachment) => String(attachment.id));
          const items = attachments.map((attachment) => {
            const previewSize =
              (attachment.sizes &&
                (attachment.sizes.thumbnail ||
                  attachment.sizes.medium ||
                  attachment.sizes.full)) ||
              null;

            return {
              url: (previewSize && previewSize.url) || attachment.url,
            };
          });

          $input.val(ids.join(","));
          renderPreview(items);
        });

        frame.open();
      });

      $picker.on("click", ".art-zone-media-gallery-picker__clear", function (event) {
        event.preventDefault();
        $input.val("");
        renderPreview([]);
      });
    });
  });
})(jQuery);
